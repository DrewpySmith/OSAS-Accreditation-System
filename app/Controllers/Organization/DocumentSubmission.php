<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\DocumentSubmissionModel;
use App\Models\OrganizationModel;
use App\Models\DocumentReviewHistoryModel;

class DocumentSubmission extends BaseController
{
    protected $documentModel;
    protected $organizationModel;

    public function __construct()
    {
        $this->documentModel = new DocumentSubmissionModel();
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        $organizationId = session()->get('organization_id');
        $data['documents'] = $this->documentModel->getByOrganization($organizationId);
        $data['organization'] = $this->organizationModel->find($organizationId);
        $data['document_types'] = DocumentSubmissionModel::DOCUMENT_TYPES;

        return view('organization/submissions/index', $data);
    }

    public function uploadForm()
    {
        $data['organization'] = $this->organizationModel->find(session()->get('organization_id'));
        $data['document_types'] = DocumentSubmissionModel::DOCUMENT_TYPES;
        return view('organization/submissions/upload', $data);
    }

    public function view($id)
    {
        $document = $this->documentModel->getDocumentWithComments($id);

        if (!$document || $document['organization_id'] != session()->get('organization_id')) {
            return redirect()->to('/organization/submissions')->with('error', 'Document not found');
        }

        $historyModel = new DocumentReviewHistoryModel();
        $reviewHistory = $historyModel
            ->select('document_review_history.*, users.username, users.role, comments.comment')
            ->join('users', 'users.id = document_review_history.user_id')
            ->join('comments', 'comments.id = document_review_history.comment_id', 'left')
            ->where('document_review_history.document_id', $id)
            ->orderBy('document_review_history.created_at', 'DESC')
            ->findAll();

        return view('organization/submissions/view', [
            'document' => $document,
            'review_history' => $reviewHistory
        ]);
    }

    public function upload()
    {
        $validation = \Config\Services::validation();

        $allowedTypes = array_keys(DocumentSubmissionModel::DOCUMENT_TYPES);

        $rules = [
            'document_type' => 'required',
            'document_title' => 'required|min_length[3]',
            'academic_year' => 'required',
            'document_file' => 'uploaded[document_file]|max_size[document_file,10240]|ext_in[document_file,pdf,doc,docx]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $file = $this->request->getFile('document_file');
        $organizationId = session()->get('organization_id');

        $documentType = trim((string) $this->request->getPost('document_type'));
        if (!in_array($documentType, $allowedTypes, true)) {
            return redirect()->back()->withInput()->with('errors', ['Invalid document type']);
        }

        $academicYear = (string) $this->request->getPost('academic_year');
        // Make folder safe: keep only letters, numbers, dash, underscore
        $safeAcademicYear = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $academicYear);
        if (empty($safeAcademicYear)) {
            return redirect()->back()->withInput()->with('errors', ['Invalid academic year']);
        }

        if ($file->isValid() && !$file->hasMoved()) {
            $org = $this->organizationModel->find($organizationId);
            $campus = $org['campus'] ?? null;

            $uploadPath = WRITEPATH . 'uploads/documents/' . $organizationId . '/' . $documentType . '/' . $safeAcademicYear . '/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $data = [
                'organization_id' => $organizationId,
                'campus' => $campus,
                'document_type' => $documentType,
                'document_title' => $this->request->getPost('document_title'),
                'file_path' => 'documents/' . $organizationId . '/' . $documentType . '/' . $safeAcademicYear . '/' . $newName,
                'file_name' => $file->getClientName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'academic_year' => $academicYear,
                'description' => $this->request->getPost('description'),
                'status' => 'pending',
                'submitted_by' => session()->get('user_id')
            ];

            if ($this->documentModel->insert($data)) {
                return redirect()->to('/organization/submissions')
                    ->with('success', 'Document uploaded successfully');
            }
        }

        return redirect()->back()->with('error', 'Failed to upload document');
    }

    public function download($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document || $document['organization_id'] != session()->get('organization_id')) {
            return redirect()->back()->with('error', 'Document not found');
        }

        $filePath = WRITEPATH . 'uploads/' . $document['file_path'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found');
        }

        return $this->response->download($filePath, null)->setFileName($document['file_name']);
    }

    public function delete($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document || $document['organization_id'] != session()->get('organization_id')) {
            return redirect()->back()->with('error', 'Document not found');
        }

        // Delete physical file
        $filePath = WRITEPATH . 'uploads/' . $document['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($this->documentModel->delete($id)) {
            return redirect()->back()->with('success', 'Document deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete document');
    }
}