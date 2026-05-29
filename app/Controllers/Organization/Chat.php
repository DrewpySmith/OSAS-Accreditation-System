<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\ChatMessageModel;

class Chat extends BaseController
{
    protected $chatModel;

    public function __construct()
    {
        $this->chatModel = new ChatMessageModel();
    }

    public function index()
    {
        $orgId = session()->get('organization_id');
        $messages = $this->chatModel->getConversation($orgId);

        $data = [
            'title' => 'Messages',
            'messages' => $messages,
            'org_id' => $orgId,
        ];

        return view('organization/chat/index', $data);
    }

    public function send()
    {
        $orgId = session()->get('organization_id');
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

    public function poll()
    {
        $orgId = session()->get('organization_id');
        $since = $this->request->getGet('since');
        $messages = $this->chatModel->getConversation($orgId, $since);

        return $this->response->setJSON([
            'messages' => $messages,
            'user_id' => session()->get('user_id'),
            'csrf' => csrf_hash(),
        ]);
    }
}
