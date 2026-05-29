<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ChatMessageModel;
use App\Models\OrganizationModel;
use App\Models\UserModel;

class Chat extends BaseController
{
    protected $chatModel;
    protected $orgModel;

    public function __construct()
    {
        $this->chatModel = new ChatMessageModel();
        $this->orgModel = new OrganizationModel();
    }

    public function index()
    {
        $inbox = $this->chatModel->getInboxList();

        $data = [
            'title' => 'Messages',
            'inbox' => $inbox,
        ];

        return view('admin/chat/index', $data);
    }

    public function conversation($orgId)
    {
        $org = $this->orgModel->find($orgId);
        if (!$org) {
            return $this->response->setJSON(['error' => 'Organization not found'], 404);
        }

        $messages = $this->chatModel->getConversation($orgId);

        // Mark unread messages as read
        $this->chatModel->markAllAsRead($orgId);

        return $this->response->setJSON([
            'success' => true,
            'organization' => $org,
            'messages' => $messages,
            'user_id' => session()->get('user_id'),
            'csrf' => csrf_hash(),
        ]);
    }

    public function send($orgId)
    {
        $message = $this->request->getPost('message');
        if (empty(trim($message))) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['message' => 'Message cannot be empty'],
                'csrf' => csrf_hash(),
            ]);
        }

        $this->chatModel->insert([
            'sender_id' => session()->get('user_id'),
            'receiver_org_id' => $orgId,
            'message' => $message,
            'is_read' => 0,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'csrf' => csrf_hash(),
        ]);
    }

    public function poll($orgId)
    {
        $since = $this->request->getGet('since');
        $messages = $this->chatModel->getConversation($orgId, $since);

        $this->chatModel->markAllAsRead($orgId);

        $inbox = $this->chatModel->getInboxList();

        return $this->response->setJSON([
            'messages' => $messages,
            'inbox' => $inbox,
            'user_id' => session()->get('user_id'),
            'csrf' => csrf_hash(),
        ]);
    }
}
