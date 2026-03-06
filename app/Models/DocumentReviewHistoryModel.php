<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentReviewHistoryModel extends Model
{
    protected $table = 'document_review_history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'document_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'comment_id'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
