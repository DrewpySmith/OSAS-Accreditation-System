<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DocumentSubmissionModel;
use App\Models\CommentModel;
use App\Models\OrganizationModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Models\DocumentReviewHistoryModel;

class Documents extends BaseController
{
    protected $documentModel;
    protected $commentModel;
    protected $organizationModel;
    protected $notificationModel;
    protected $userModel;
    protected $documentReviewHistoryModel;

    public function __construct()
    {
        $this->documentModel = new DocumentSubmissionModel();
        $this->commentModel = new CommentModel();
        $this->organizationModel = new OrganizationModel();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->documentReviewHistoryModel = new DocumentReviewHistoryModel();
    }

    public function index()
    {
        $filters = [];

        if ($this->request->getGet('organization_id')) {
            $filters['organization_id'] = $this->request->getGet('organization_id');
        }

        if ($this->request->getGet('document_type')) {
            $filters['document_type'] = $this->request->getGet('document_type');
        }

        if ($this->request->getGet('status')) {
            $filters['status'] = $this->request->getGet('status');
        }

        if ($this->request->getGet('campus')) {
            $filters['campus'] = $this->request->getGet('campus');
        }

        if ($this->request->getGet('academic_year')) {
            $filters['academic_year'] = $this->request->getGet('academic_year');
        }

        $data['documents'] = $this->documentModel->getAllSubmissions($filters);
        $data['organizations'] = $this->organizationModel->findAll();
        $db = \Config\Database::connect();
        $data['academic_years'] = $db->table('academic_years')->orderBy('year', 'DESC')->get()->getResultArray();
        $data['campuses'] = OrganizationModel::CAMPUSES;
        $data['selected_filters'] = $filters;

        // Unified document types from model
        $data['document_types'] = DocumentSubmissionModel::DOCUMENT_TYPES;

        return view('admin/documents/index', $data);
    }

    public function view($id)
    {
        $data['document'] = $this->documentModel->getDocumentWithComments($id);

        if (!$data['document']) {
            return redirect()->to('/admin/documents')->with('error', 'Document not found');
        }

        $history = $this->documentReviewHistoryModel
            ->select('document_review_history.*, users.username, users.role, comments.comment')
            ->join('users', 'users.id = document_review_history.user_id')
            ->join('comments', 'comments.id = document_review_history.comment_id', 'left')
            ->where('document_review_history.document_id', $id)
            ->orderBy('document_review_history.created_at', 'DESC')
            ->findAll();

        $data['review_history'] = $history;

        return view('admin/documents/view', $data);
    }

    public function download($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return $this->response
                ->setStatusCode(404)
                ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->setBody('Document not found');
        }

        $filePath = WRITEPATH . 'uploads/' . $document['file_path'];

        if (!file_exists($filePath)) {
            return $this->response
                ->setStatusCode(404)
                ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->setBody('File not found');
        }

        return $this->response->download($filePath, null)->setFileName($document['file_name']);
    }

    public function preview($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return $this->response
                ->setStatusCode(404)
                ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->setBody('Document not found');
        }

        $filePath = WRITEPATH . 'uploads/' . $document['file_path'];

        if (!file_exists($filePath)) {
            return $this->response
                ->setStatusCode(404)
                ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->setBody('File not found');
        }

        $fileName = $document['file_name'] ?? basename($filePath);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Prefer the stored client MIME type (reliable on Windows where finfo may be unavailable)
        $mime = $document['file_type'] ?? '';

        if (empty($mime)) {
            $mime = 'application/octet-stream';
        }

        // Extension-based fallback/override for common previewable types
        if ($ext === 'pdf') {
            $mime = 'application/pdf';
        } elseif (in_array($ext, ['png'], true)) {
            $mime = 'image/png';
        } elseif (in_array($ext, ['jpg', 'jpeg'], true)) {
            $mime = 'image/jpeg';
        } elseif (in_array($ext, ['gif'], true)) {
            $mime = 'image/gif';
        } elseif (in_array($ext, ['webp'], true)) {
            $mime = 'image/webp';
        }

        $size = filesize($filePath);
        $range = $this->request->getHeaderLine('Range');

        // Default: full content
        $start = 0;
        $end = $size - 1;
        $statusCode = 200;

        // Support byte range requests (required by most browser PDF viewers)
        if (!empty($range) && preg_match('/bytes=\s*(\d*)-(\d*)/i', $range, $matches)) {
            if ($matches[1] !== '') {
                $start = (int) $matches[1];
            }
            if ($matches[2] !== '') {
                $end = (int) $matches[2];
            }

            if ($start > $end || $start >= $size) {
                return $this->response
                    ->setStatusCode(416)
                    ->setHeader('Content-Range', 'bytes */' . $size);
            }

            $end = min($end, $size - 1);
            $statusCode = 206;
        }

        $length = ($end - $start) + 1;

        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            return $this->response
                ->setStatusCode(404)
                ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->setBody('File not found');
        }

        if ($start > 0) {
            fseek($handle, $start);
        }

        $body = '';
        $remaining = $length;
        while ($remaining > 0 && !feof($handle)) {
            $chunk = fread($handle, min(8192, $remaining));
            if ($chunk === false) {
                break;
            }
            $body .= $chunk;
            $remaining -= strlen($chunk);
        }
        fclose($handle);

        $resp = $this->response
            ->setStatusCode($statusCode)
            ->setHeader('Content-Type', $mime)
            ->setHeader('Accept-Ranges', 'bytes')
            ->setHeader('Content-Length', (string) $length);

        // Only set Content-Disposition for non-previewable files
        if (!in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
            $resp = $resp->setHeader('Content-Disposition', 'attachment; filename="' . addslashes($fileName) . '"');
        }

        if ($statusCode === 206) {
            $resp = $resp->setHeader('Content-Range', 'bytes ' . $start . '-' . $end . '/' . $size);
        }

        return $resp->setBody($body);
    }

    public function addComment($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Document not found',
                'csrf' => csrf_hash()
            ]);
        }

        $payload = $this->request->getJSON(true);
        $commentText = $payload['comment'] ?? $this->request->getPost('comment');

        if (empty($commentText)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Comment cannot be empty',
                'csrf' => csrf_hash()
            ]);
        }

        $commentData = [
            'document_id' => $id,
            'user_id' => session()->get('user_id'),
            'comment' => $commentText
        ];

        if ($this->commentModel->addComment($commentData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Comment added successfully',
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add comment',
            'csrf' => csrf_hash()
        ]);
    }

    public function updateStatus($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Document not found',
                'csrf' => csrf_hash()
            ]);
        }

        $payload = $this->request->getJSON(true);
        $status = $payload['status'] ?? $this->request->getPost('status');

        $allowed = ['pending', 'reviewed', 'approved', 'rejected'];
        if (empty($status) || !in_array($status, $allowed, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid status',
                'csrf' => csrf_hash()
            ]);
        }

        $fromStatus = $document['status'] ?? null;

        $data = [
            'status' => $status,
            'reviewed_by' => session()->get('user_id'),
            'reviewed_at' => date('Y-m-d H:i:s')
        ];

        if ($this->documentModel->update($id, $data)) {
            // Review history
            $this->documentReviewHistoryModel->insert([
                'document_id' => $id,
                'user_id' => session()->get('user_id'),
                'action' => 'status_change',
                'from_status' => $fromStatus,
                'to_status' => $status,
                'comment_id' => null
            ]);

            // Notify organization users
            $orgUsers = $this->userModel->getOrganizationUsers($document['organization_id']);
            foreach ($orgUsers as $user) {
                $this->notificationModel->insert([
                    'user_id' => $user['id'],
                    'title' => 'Document Status Updated',
                    'message' => 'Admin updated the status of your submitted document "' . ($document['document_title'] ?? 'Document') . '" to: ' . ucfirst($status),
                    'type' => 'status',
                    'related_id' => $id,
                    'is_read' => 0
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Document status updated successfully',
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update document status',
            'csrf' => csrf_hash()
        ]);
    }

    public function printDocs()
    {
        $filters = [];
        if ($this->request->getGet('organization_id'))
            $filters['organization_id'] = $this->request->getGet('organization_id');
        if ($this->request->getGet('document_type'))
            $filters['document_type'] = $this->request->getGet('document_type');
        if ($this->request->getGet('status'))
            $filters['status'] = $this->request->getGet('status');
        if ($this->request->getGet('campus'))
            $filters['campus'] = $this->request->getGet('campus');

        $data['documents'] = $this->documentModel->getAllSubmissions($filters);
        $data['title'] = 'Document Submissions' . (!empty($filters['campus']) ? ' - ' . $filters['campus'] . ' Campus' : '');
        $data['selected_campus'] = $filters['campus'] ?? '';

        return view('admin/documents/print', $data);
    }
}