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

        $announcements = $this->announcementModel
            ->getForOrg($orgId, $campus);

        $unreadCount = $this->announcementModel
            ->getUnreadCount($orgId, $campus);

        $data = [
            'title' => 'Announcements',
            'announcements' => $announcements,
            'unread_count' => $unreadCount,
        ];

        return view('organization/announcements/index', $data);
    }

    public function markAsRead($id)
    {
        $orgId = session()->get('organization_id');
        $this->readModel->markAsRead($id, $orgId);

        return $this->response->setJSON([
            'success' => true,
            'csrf' => csrf_hash(),
        ]);
    }
}
