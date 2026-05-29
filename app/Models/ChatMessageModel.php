<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table = 'chat_messages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'sender_id',
        'receiver_org_id',
        'message',
        'is_read',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'sender_id' => 'required|numeric',
        'receiver_org_id' => 'required|numeric',
        'message' => 'required',
    ];

    public function getConversation($orgId, $since = null, $limit = 100)
    {
        $builder = $this->where('receiver_org_id', $orgId)
            ->orderBy('created_at', 'ASC')
            ->limit($limit);

        if ($since) {
            $builder->where('created_at >', $since);
        }

        return $builder->findAll();
    }

    public function getLastMessage($orgId)
    {
        return $this->where('receiver_org_id', $orgId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function markAllAsRead($orgId)
    {
        return $this->where('receiver_org_id', $orgId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();
    }

    public function getUnreadCount($orgId)
    {
        return $this->where('receiver_org_id', $orgId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function getInboxList()
    {
        $db = \Config\Database::connect();

        return $db->table('organizations')
            ->select('organizations.id, organizations.name, organizations.acronym, 
                (SELECT message FROM chat_messages WHERE receiver_org_id = organizations.id ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT created_at FROM chat_messages WHERE receiver_org_id = organizations.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
                (SELECT COUNT(*) FROM chat_messages WHERE receiver_org_id = organizations.id AND is_read = 0) as unread_count')
            ->where('organizations.status', 'active')
            ->orderBy('last_message_time', 'DESC')
            ->get()
            ->getResultArray();
    }
}
