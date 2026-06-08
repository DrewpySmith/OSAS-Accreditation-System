<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\AnnouncementAttachmentModel;
use App\Models\AnnouncementCommentModel;
use App\Models\OrganizationModel;

class Announcements extends BaseController
{
    protected $announcementModel;
    protected $attachmentModel;
    protected $commentModel;
    protected $orgModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->attachmentModel = new AnnouncementAttachmentModel();
        $this->commentModel = new AnnouncementCommentModel();
        $this->orgModel = new OrganizationModel();
    }

    public function index()
    {
        $announcements = $this->announcementModel
            ->select('announcements.*, users.username as sender_name')
            ->join('users', 'users.id = announcements.sender_id', 'left')
            ->orderBy('is_pinned', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Attach attachment counts and comment counts
        foreach ($announcements as &$a) {
            $a['attachment_count'] = $this->attachmentModel->where('announcement_id', $a['id'])->countAllResults();
            $a['comment_count'] = $this->commentModel->getCount($a['id']);
        }

        $data = [
            'title' => 'Announcements',
            'announcements' => $announcements,
            'campuses' => OrganizationModel::CAMPUSES,
            'organizations' => $this->orgModel->findAll(),
        ];

        return view('admin/announcements/index', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'message' => 'required',
            'target_type' => 'required|in_list[all,campus,organization]',
            'summary' => 'permit_empty|max_length[500]',
            'content' => 'permit_empty',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'category' => 'required|in_list[general,update,maintenance,feature,security,announcement]',
            'author' => 'permit_empty|max_length[255]',
            'is_pinned' => 'permit_empty',
            'expires_at' => 'permit_empty',
        ];

        $targetType = $this->request->getPost('target_type');
        if ($targetType === 'campus') {
            $rules['target_value'] = 'required';
        } elseif ($targetType === 'organization') {
            $rules['target_value'] = 'required|numeric';
        }

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => \Config\Services::validation()->getErrors(),
                    'csrf' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', \Config\Services::validation()->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'message' => $this->request->getPost('message'),
            'sender_id' => session()->get('user_id'),
            'target_type' => $targetType,
            'target_value' => $this->request->getPost('target_value'),
            'summary' => $this->request->getPost('summary'),
            'content' => $this->request->getPost('content'),
            'priority' => $this->request->getPost('priority') ?? 'medium',
            'category' => $this->request->getPost('category') ?? 'general',
            'author' => $this->request->getPost('author'),
            'is_pinned' => $this->request->getPost('is_pinned') ? 1 : 0,
            'expires_at' => $this->request->getPost('expires_at') ?: null,
        ];

        $announcementId = $this->announcementModel->insert($data);

        if ($announcementId) {
            $this->handleAttachments($announcementId);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Announcement created successfully',
                    'csrf' => csrf_hash(),
                ]);
            }
            return redirect()->to('/admin/announcements')->with('success', 'Announcement created successfully');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create announcement',
                'csrf' => csrf_hash(),
            ]);
        }
        return redirect()->back()->with('error', 'Failed to create announcement');
    }

    public function update($id)
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'message' => 'required',
            'target_type' => 'required|in_list[all,campus,organization]',
            'summary' => 'permit_empty|max_length[500]',
            'content' => 'permit_empty',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'category' => 'required|in_list[general,update,maintenance,feature,security,announcement]',
            'author' => 'permit_empty|max_length[255]',
            'is_pinned' => 'permit_empty',
            'expires_at' => 'permit_empty',
        ];

        $targetType = $this->request->getPost('target_type');
        if ($targetType === 'campus') {
            $rules['target_value'] = 'required';
        } elseif ($targetType === 'organization') {
            $rules['target_value'] = 'required|numeric';
        }

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => \Config\Services::validation()->getErrors(),
                    'csrf' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', \Config\Services::validation()->getErrors());
        }

        $this->announcementModel->update($id, [
            'title' => $this->request->getPost('title'),
            'message' => $this->request->getPost('message'),
            'target_type' => $targetType,
            'target_value' => $this->request->getPost('target_value'),
            'summary' => $this->request->getPost('summary'),
            'content' => $this->request->getPost('content'),
            'priority' => $this->request->getPost('priority') ?? 'medium',
            'category' => $this->request->getPost('category') ?? 'general',
            'author' => $this->request->getPost('author'),
            'is_pinned' => $this->request->getPost('is_pinned') ? 1 : 0,
            'expires_at' => $this->request->getPost('expires_at') ?: null,
        ]);

        $this->handleAttachments($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement updated successfully',
                'csrf' => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/announcements')->with('success', 'Announcement updated successfully');
    }

    public function delete($id)
    {
        $this->attachmentModel->deleteByAnnouncement($id);
        $this->announcementModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement deleted',
                'csrf' => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/announcements')->with('success', 'Announcement deleted');
    }

    public function toggleActive($id)
    {
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found', 'csrf' => csrf_hash()]);
        }

        $this->announcementModel->update($id, [
            'is_active' => $announcement['is_active'] ? 0 : 1,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'is_active' => !$announcement['is_active'],
            'csrf' => csrf_hash(),
        ]);
    }

    public function togglePin($id)
    {
        if (!$this->announcementModel->togglePin($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found', 'csrf' => csrf_hash()]);
        }

        $announcement = $this->announcementModel->find($id);
        return $this->response->setJSON([
            'success' => true,
            'is_pinned' => (bool) $announcement['is_pinned'],
            'csrf' => csrf_hash(),
        ]);
    }

    public function downloadAttachment($id)
    {
        $attachment = $this->attachmentModel->find($id);
        if (!$attachment) {
            return redirect()->back()->with('error', 'Attachment not found');
        }

        $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on disk');
        }

        return $this->response->download($filePath, null)->setFileName($attachment['file_name']);
    }

    public function addComment($id)
    {
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Announcement not found', 'csrf' => csrf_hash()]);
        }

        $commentText = $this->request->getPost('comment');
        if (empty(trim($commentText))) {
            return $this->response->setJSON(['success' => false, 'message' => 'Comment cannot be empty', 'csrf' => csrf_hash()]);
        }

        $this->commentModel->insert([
            'announcement_id' => $id,
            'user_id' => session()->get('user_id'),
            'comment' => $commentText,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Comment posted',
            'csrf' => csrf_hash(),
        ]);
    }

    public function deleteComment($commentId)
    {
        $comment = $this->commentModel->find($commentId);
        if (!$comment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Comment not found', 'csrf' => csrf_hash()]);
        }

        // Only comment owner or admin can delete
        if ($comment['user_id'] != session()->get('user_id') && session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized', 'csrf' => csrf_hash()]);
        }

        $this->commentModel->delete($commentId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Comment deleted',
            'csrf' => csrf_hash(),
        ]);
    }

    public function getOrgs()
    {
        $campus = $this->request->getGet('campus');
        $query = $this->orgModel->where('status', 'active');
        if ($campus) {
            $query->where('campus', $campus);
        }

        return $this->response->setJSON([
            'organizations' => $query->findAll(),
            'csrf' => csrf_hash(),
        ]);
    }

    private function handleAttachments($announcementId)
    {
        $files = $this->request->getFileMultiple('attachments');
        if (!$files || !is_array($files)) {
            return;
        }

        $uploadPath = WRITEPATH . 'uploads/announcements/' . $announcementId . '/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $maxSize = 10 * 1024 * 1024; // 10MB
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/png',
            'image/gif',
        ];

        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                if ($file->getSize() > $maxSize) { continue; }
                if (!in_array($file->getMimeType(), $allowedTypes)) { continue; }

                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);

                $this->attachmentModel->insert([
                    'announcement_id' => $announcementId,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'announcements/' . $announcementId . '/' . $newName,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
    }
}
