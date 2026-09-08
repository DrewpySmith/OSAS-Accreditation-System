<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementAttachmentModel extends Model
{
    protected $table = 'announcement_attachments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['announcement_id', 'filename', 'filepath', 'filetype', 'filesize'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $dateFormat = 'datetime';
}
