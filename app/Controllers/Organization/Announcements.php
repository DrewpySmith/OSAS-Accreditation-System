<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\AnnouncementReadModel;
use App\Models\OrganizationModel;

class Announcements extends BaseController
{
    protected $announcementModel;
    protected $readModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->readModel = new AnnouncementReadModel();
    }

    public function index()
    {
        $orgId = session()->get('organization_id');
        $org = model(OrganizationModel::class)->find($orgId);
        $campus = $org ? $org['campus'] : null;
        $filter = $this->request->getGet('filter') ?? 'all';
        $priority = $this->request->getGet('priority');
        $search = $this->request->getGet('search');
        $filters = [];
        if ($priority) $filters['priority'] = $priority;
        if ($search) $filters['search'] = $search;
        if ($filter === 'unread' || $filter === 'pending') { $filters['ack'] = 'pending'; $filters['orgId'] = $orgId; }
        elseif ($filter === 'acknowledged') { $filters['ack'] = 'acknowledged'; $filters['orgId'] = $orgId; }

        $announcements = $this->announcementModel->getForOrg($orgId, $campus, null, $filters);
        if ($filter === 'expired') {
            $announcements = array_filter($announcements, fn($a) => !empty($a['expires_at']) && strtotime($a['expires_at']) < time());
        }
        foreach ($announcements as &$a) {
            $a['is_ack'] = $this->readModel->isAcknowledged($a['id'], $orgId);
            $a['is_read'] = $this->readModel->isRead($a['id'], $orgId);
            $a['attachments'] = $this->announcementModel->getAttachments($a['id']);
        }
        $unreadCount = $this->announcementModel->getUnreadCount($orgId, $campus);
        $pinned = array_filter($announcements, fn($a) => $a['is_pinned'] && in_array($a['priority'], ['urgent','critical']) && !$a['is_ack']);
        $regular = array_filter($announcements, fn($a) => !($a['is_pinned'] && in_array($a['priority'], ['urgent','critical']) && !$a['is_ack']));

        return view('organization/announcements/index', [
            'title' => 'Announcements',
            'announcements' => $regular,
            'pinned' => $pinned,
            'unread_count' => $unreadCount,
            'filter' => $filter,
            'priority' => $priority,
            'search' => $search,
        ]);
    }

    public function markAsRead($id)
    {
        $orgId = session()->get('organization_id');
        $this->readModel->markAsRead($id, $orgId);
        return $this->response->setJSON(['success' => true, 'csrf' => csrf_hash()]);
    }

    public function acknowledge($id)
    {
        $orgId = session()->get('organization_id');
        $this->readModel->acknowledge($id, $orgId);
        return $this->response->setJSON(['success' => true, 'csrf' => csrf_hash()]);
    }

    public function unreadCount()
    {
        $orgId = session()->get('organization_id');
        $org = model(OrganizationModel::class)->find($orgId);
        $campus = $org ? $org['campus'] : null;
        return $this->response->setJSON(['count' => $this->announcementModel->getUnreadCount($orgId, $campus), 'csrf' => csrf_hash()]);
    }

    public function downloadAttachment($id)
    {
        $att = model(\App\Models\AnnouncementAttachmentModel::class)->find($id);
        if (!$att) throw new \CodeIgniter\Exceptions\PageNotFoundException();
        $a = $this->announcementModel->find($att['announcement_id']);
        $orgId = session()->get('organization_id');
        $org = model(OrganizationModel::class)->find($orgId);
        $allowed = $this->announcementModel->getForOrg($orgId, $org['campus'] ?? null);
        if (!in_array($att['announcement_id'], array_column($allowed, 'id'))) throw new \CodeIgniter\Exceptions\PageNotFoundException();
        return $this->response->download(WRITEPATH . 'uploads/announcements/' . basename($att['filepath']), null)->setFileName($att['filename']);
    }
}
