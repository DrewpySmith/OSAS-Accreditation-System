<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $data['notifications'] = $this->notificationModel->getByUser($userId);
        $data['unread_count'] = $this->notificationModel->getUnreadCount($userId);
        
        return view('organization/notifications', $data);
    }

    public function markAsRead($id)
    {
        $notification = $this->notificationModel->find($id);
        
        if (!$notification || $notification['user_id'] != session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found',
                'csrf' => csrf_hash()
            ]);
        }

        if ($this->notificationModel->markAsRead($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read',
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to mark notification as read',
            'csrf' => csrf_hash()
        ]);
    }

    public function markAllAsRead()
    {
        $userId = session()->get('user_id');
        
        if ($this->notificationModel->markAllAsRead($userId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All notifications marked as read',
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to mark notifications as read',
            'csrf' => csrf_hash()
        ]);
    }

    public function getUnreadCount()
    {
        $userId = session()->get('user_id');
        $count = $this->notificationModel->getUnreadCount($userId);
        
        return $this->response->setJSON([
            'success' => true,
            'count' => $count
        ]);
    }
}