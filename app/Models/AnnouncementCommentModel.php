<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementCommentModel extends Model
{
    protected $table = 'announcement_comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'announcement_id',
        'user_id',
        'comment',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getByAnnouncement($announcementId)
    {
        return $this->select('announcement_comments.*, users.username, users.role')
            ->join('users', 'users.id = announcement_comments.user_id', 'left')
            ->where('announcement_comments.announcement_id', $announcementId)
            ->orderBy('announcement_comments.created_at', 'ASC')
            ->findAll();
    }

    public function getCount($announcementId)
    {
        return $this->where('announcement_id', $announcementId)->countAllResults();
    }
}
