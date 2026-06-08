<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementAttachmentModel extends Model
{
    protected $table = 'announcement_attachments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'announcement_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getByAnnouncement($announcementId)
    {
        return $this->where('announcement_id', $announcementId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function deleteByAnnouncement($announcementId)
    {
        $attachments = $this->where('announcement_id', $announcementId)->findAll();

        foreach ($attachments as $attachment) {
            $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return $this->where('announcement_id', $announcementId)->delete();
    }

    public function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
