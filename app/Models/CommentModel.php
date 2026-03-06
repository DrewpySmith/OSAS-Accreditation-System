<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'document_id',
        'user_id',
        'comment'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'document_id' => 'required|numeric',
        'user_id' => 'required|numeric',
        'comment' => 'required',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getByDocument($documentId)
    {
        return $this->select('comments.*, users.username, users.role')
            ->join('users', 'users.id = comments.user_id')
            ->where('document_id', $documentId)
            ->orderBy('comments.created_at', 'DESC')
            ->findAll();
    }

    public function addComment($data)
    {
        $result = $this->insert($data);
        $commentId = $this->getInsertID();
        
        if ($result) {
            // Get document and organization info
            $documentModel = new \App\Models\DocumentSubmissionModel();
            $document = $documentModel->find($data['document_id']);

            // Add review history entry
            $historyModel = new \App\Models\DocumentReviewHistoryModel();
            $historyModel->insert([
                'document_id' => $data['document_id'],
                'user_id' => $data['user_id'],
                'action' => 'comment',
                'from_status' => null,
                'to_status' => null,
                'comment_id' => $commentId
            ]);
            
            if ($document) {
                // Create notification for organization
                $notificationModel = new \App\Models\NotificationModel();
                $userModel = new \App\Models\UserModel();
                
                // Get organization users
                $orgUsers = $userModel->getOrganizationUsers($document['organization_id']);
                
                foreach ($orgUsers as $user) {
                    $notificationModel->insert([
                        'user_id' => $user['id'],
                        'title' => 'New Comment on Document',
                        'message' => 'Admin has commented on your submitted document: ' . $document['document_title'],
                        'type' => 'comment',
                        'related_id' => $data['document_id'],
                        'is_read' => 0
                    ]);
                }
            }
        }
        
        return $commentId;
    }
}