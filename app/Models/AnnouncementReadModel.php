<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementReadModel extends Model
{
    protected $table = 'announcement_reads';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'announcement_id',
        'organization_id',
        'read_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function markAsRead($announcementId, $orgId)
    {
        $exists = $this->where('announcement_id', $announcementId)
            ->where('organization_id', $orgId)
            ->first();

        if ($exists) {
            return true;
        }

        return $this->insert([
            'announcement_id' => $announcementId,
            'organization_id' => $orgId,
            'read_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function isRead($announcementId, $orgId)
    {
        return (bool) $this->where('announcement_id', $announcementId)
            ->where('organization_id', $orgId)
            ->first();
    }
}
