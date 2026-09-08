<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\AnnouncementTargetModel;
use App\Models\AnnouncementAttachmentModel;
use App\Models\OrganizationModel;

class Announcements extends BaseController
{
    protected $announcementModel;
    protected $targetModel;
    protected $attachmentModel;
    protected $orgModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->targetModel = new AnnouncementTargetModel();
        $this->attachmentModel = new AnnouncementAttachmentModel();
        $this->orgModel = new OrganizationModel();
    }

    public function index()
    {
        $priority = $this->request->getGet('priority');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        $campus = $this->request->getGet('campus');

        $builder = $this->announcementModel->select('announcements.*, users.username as sender_name')
            ->join('users', 'users.id = announcements.sender_id', 'left')
            ->orderBy('announcements.is_pinned', 'DESC')
            ->orderBy("CASE announcements.priority WHEN 'critical' THEN 3 WHEN 'urgent' THEN 2 ELSE 1 END", 'DESC', false)
            ->orderBy('announcements.created_at', 'DESC');

        if ($priority && in_array($priority, AnnouncementModel::PRIORITIES)) $builder->where('priority', $priority);
        if ($status && in_array($status, AnnouncementModel::STATUSES)) $builder->where('status', $status);
        if ($search) $builder->like('announcements.title', $search);
        if ($campus) {
            $builder->groupStart()
                ->where('announcements.target_value', $campus)->where('announcements.target_type', 'campus')
                ->orWhereIn('announcements.id', function($b) use ($campus) {
                    return $b->select('announcement_id')->from('announcement_targets')->where('target_type', 'campus')->where('target_value', $campus);
                })
            ->groupEnd();
        }

        $announcements = $builder->findAll();
        foreach ($announcements as &$a) {
            $a['targets'] = $this->announcementModel->getTargets($a['id']);
            $a['attachments'] = $this->announcementModel->getAttachments($a['id']);
            $c = $this->announcementModel->getCompliance($a['id']);
            $a['ackedCount'] = $c['ackedCount'];
            $a['pendingCount'] = $c['pendingCount'];
            $a['totalTargets'] = $c['total'];
        }

        return view('admin/announcements/index', [
            'title' => 'Announcements',
            'announcements' => $announcements,
            'campuses' => OrganizationModel::CAMPUSES,
            'organizations' => $this->orgModel->findAll(),
            'filters' => ['priority' => $priority, 'status' => $status, 'search' => $search, 'campus' => $campus],
        ]);
    }

    private function validatePayload()
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'message' => 'required',
            'priority' => 'required|in_list[normal,urgent,critical]',
            'status' => 'required|in_list[draft,published,expired,archived]',
        ];
        return $rules;
    }

    private function syncTargets($announcementId)
    {
        $this->targetModel->where('announcement_id', $announcementId)->delete();
        $campuses = $this->request->getPost('target_campuses');
        $orgIds = $this->request->getPost('target_orgs');
        $targetType = $this->request->getPost('target_type');
        if ($targetType === 'all') {
            $this->targetModel->insert(['announcement_id' => $announcementId, 'target_type' => 'all', 'target_value' => 'all']);
        } else {
            if (!empty($campuses) && is_array($campuses)) {
                foreach ($campuses as $c) {
                    if (in_array($c, OrganizationModel::CAMPUSES)) $this->targetModel->insert(['announcement_id' => $announcementId, 'target_type' => 'campus', 'target_value' => $c]);
                }
            }
            if (!empty($orgIds) && is_array($orgIds)) {
                foreach ($orgIds as $oid) {
                    if (is_numeric($oid)) $this->targetModel->insert(['announcement_id' => $announcementId, 'target_type' => 'organization', 'target_value' => $oid]);
                }
            }
            if (empty($campuses) && empty($orgIds)) {
                $val = $this->request->getPost('target_value');
                if ($val) $this->targetModel->insert(['announcement_id' => $announcementId, 'target_type' => $targetType, 'target_value' => $val]);
            }
        }
    }

    private function handleAttachments($announcementId)
    {
        $files = $this->request->getFiles();
        if (empty($files['attachments'])) return;
        $attachments = $files['attachments'];
        if (!is_array($attachments)) $attachments = [$attachments];
        $allowedMimes = ['application/pdf','image/jpeg','image/png','image/jpg','image/webp','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $maxSize = 10 * 1024 * 1024;
        $count = 0;
        foreach ($attachments as $file) {
            if (!$file || $file->getError() === 4 || !$file->isValid() || $file->hasMoved() || $file->getSize() === 0) continue;
            try { $mime = $file->getMimeType(); $size = $file->getSize(); } catch (\Throwable $e) { continue; }
            if (!in_array($mime, $allowedMimes)) continue;
            if ($size > $maxSize) continue;
            $count++;
            if ($count > 3) break;
            $newName = $file->getRandomName();
            try { $file->move(WRITEPATH . 'uploads/announcements', $newName); } catch (\Throwable $e) { continue; }
            $this->attachmentModel->insert([
                'announcement_id' => $announcementId,
                'filename' => $file->getClientName(),
                'filepath' => 'writable/uploads/announcements/' . $newName,
                'filetype' => $mime,
                'filesize' => $size,
            ]);
        }
    }

    private function sendUrgentEmail($announcement)
    {
        if (!in_array($announcement['priority'], ['urgent','critical'])) return;
        if ($announcement['status'] !== 'published') return;
        $compliance = $this->announcementModel->getCompliance($announcement['id']);
        $emails = [];
        foreach (array_merge($compliance['pending'], $compliance['acked']) as $org) {
            if (!empty($org['officer_email'])) $emails[] = $org['officer_email'];
            if (!empty($org['adviser_email'])) $emails[] = $org['adviser_email'];
        }
        $emails = array_unique(array_filter($emails));
        if (empty($emails)) return;
        $email = \Config\Services::email();
        $email->setSubject('[SKSU OSAS] ' . $announcement['title']);
        $email->setMessage($announcement['message'] . "\n\nAction: " . ($announcement['action_url'] ?? base_url('organization/announcements')));
        foreach ($emails as $e) {
            $email->setTo($e);
            try { $email->send(false); } catch (\Throwable $ex) {}
            $email->clear();
        }
    }

    public function store()
    {
        if (!$this->validate($this->validatePayload())) {
            return $this->response->setJSON(['success' => false, 'errors' => \Config\Services::validation()->getErrors(), 'csrf' => csrf_hash()]);
        }
        $data = [
            'title' => $this->request->getPost('title'),
            'message' => $this->request->getPost('message'),
            'sender_id' => session()->get('user_id'),
            'target_type' => $this->request->getPost('target_type') ?? 'all',
            'target_value' => $this->request->getPost('target_value'),
            'priority' => $this->request->getPost('priority') ?? 'normal',
            'status' => $this->request->getPost('status') ?? 'published',
            'is_pinned' => $this->request->getPost('is_pinned') ? 1 : 0,
            'publish_at' => $this->request->getPost('publish_at') ?: null,
            'expires_at' => $this->request->getPost('expires_at') ?: null,
            'action_label' => $this->request->getPost('action_label'),
            'action_url' => $this->request->getPost('action_url'),
            'is_active' => ($this->request->getPost('status') ?? 'published') === 'published' ? 1 : 0,
        ];
        $id = $this->announcementModel->insert($data, true);
        $this->syncTargets($id);
        $this->handleAttachments($id);
        $ann = $this->announcementModel->find($id);
        $this->sendUrgentEmail($ann);
        if ($this->request->isAJAX()) return $this->response->setJSON(['success' => true, 'message' => 'Announcement created', 'csrf' => csrf_hash()]);
        return redirect()->to('/admin/announcements')->with('success', 'Announcement created');
    }

    public function update($id)
    {
        if (!$this->validate($this->validatePayload())) {
            return $this->response->setJSON(['success' => false, 'errors' => \Config\Services::validation()->getErrors(), 'csrf' => csrf_hash()]);
        }
        $data = [
            'title' => $this->request->getPost('title'),
            'message' => $this->request->getPost('message'),
            'target_type' => $this->request->getPost('target_type') ?? 'all',
            'target_value' => $this->request->getPost('target_value'),
            'priority' => $this->request->getPost('priority') ?? 'normal',
            'status' => $this->request->getPost('status') ?? 'published',
            'is_pinned' => $this->request->getPost('is_pinned') ? 1 : 0,
            'publish_at' => $this->request->getPost('publish_at') ?: null,
            'expires_at' => $this->request->getPost('expires_at') ?: null,
            'action_label' => $this->request->getPost('action_label'),
            'action_url' => $this->request->getPost('action_url'),
            'is_active' => ($this->request->getPost('status') ?? 'published') === 'published' ? 1 : 0,
        ];
        $this->announcementModel->update($id, $data);
        $this->syncTargets($id);
        if ($this->request->getPost('remove_attachments')) {
            $ids = $this->request->getPost('remove_attachments');
            if (is_array($ids)) foreach ($ids as $aid) $this->attachmentModel->delete($aid);
        }
        $this->handleAttachments($id);
        if ($this->request->isAJAX()) return $this->response->setJSON(['success' => true, 'message' => 'Announcement updated', 'csrf' => csrf_hash()]);
        return redirect()->to('/admin/announcements')->with('success', 'Announcement updated');
    }

    public function delete($id)
    {
        $this->announcementModel->delete($id);
        if ($this->request->isAJAX()) return $this->response->setJSON(['success' => true, 'message' => 'Deleted', 'csrf' => csrf_hash()]);
        return redirect()->to('/admin/announcements')->with('success', 'Deleted');
    }

    public function toggleActive($id)
    {
        $a = $this->announcementModel->find($id);
        if (!$a) return $this->response->setJSON(['success' => false, 'message' => 'Not found', 'csrf' => csrf_hash()]);
        $new = $a['is_active'] ? 0 : 1;
        $this->announcementModel->update($id, ['is_active' => $new, 'status' => $new ? 'published' : 'archived']);
        return $this->response->setJSON(['success' => true, 'is_active' => $new, 'csrf' => csrf_hash()]);
    }

    public function get($id)
    {
        $a = $this->announcementModel->find($id);
        if (!$a) return $this->response->setJSON(['success' => false, 'csrf' => csrf_hash()]);
        $a['targets'] = $this->announcementModel->getTargets($id);
        $a['attachments'] = $this->announcementModel->getAttachments($id);
        return $this->response->setJSON(['success' => true, 'announcement' => $a, 'csrf' => csrf_hash()]);
    }

    public function compliance($id)
    {
        $c = $this->announcementModel->getCompliance($id);
        return $this->response->setJSON(['success' => true, 'compliance' => $c, 'csrf' => csrf_hash()]);
    }

    public function resend($id)
    {
        $ann = $this->announcementModel->find($id);
        if (!$ann) return $this->response->setJSON(['success' => false, 'csrf' => csrf_hash()]);
        $this->sendUrgentEmail($ann);
        return $this->response->setJSON(['success' => true, 'message' => 'Resent', 'csrf' => csrf_hash()]);
    }

    public function downloadAttachment($id)
    {
        $att = $this->attachmentModel->find($id);
        if (!$att) throw new \CodeIgniter\Exceptions\PageNotFoundException();
        return $this->response->download(WRITEPATH . 'uploads/announcements/' . basename($att['filepath']), null)->setFileName($att['filename']);
    }

    public function getOrgs()
    {
        $campus = $this->request->getGet('campus');
        $q = $this->orgModel->where('status', 'active');
        if ($campus) $q->where('campus', $campus);
        return $this->response->setJSON(['organizations' => $q->findAll(), 'csrf' => csrf_hash()]);
    }
}
