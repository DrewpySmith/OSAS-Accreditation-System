<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentSubmissionModel extends Model
{
    public const DOCUMENT_TYPES = [
        'application_letter' => 'Application Letter Form',
        'officer_list' => 'Lists of Officers',
        'commitment_forms' => 'Commitment Forms of Officers and Advisers',
        'constitution_bylaws' => 'Constitution and By-Laws',
        'org_structure' => 'Organizational Structure',
        'calendar_activities' => 'Plan and Calendar of Activities',
        'financial_report' => 'Audited Financial Report',
        'program_expenditures' => 'Program of Expenditures',
        'accomplishment_report' => 'Accomplishment Reports',
        'other' => 'Other Documents'
    ];

    protected $table = 'document_submissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'organization_id',
        'campus',
        'document_type',
        'document_title',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'academic_year',
        'description',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at'
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
        'organization_id' => 'required|numeric',
        'document_type' => 'required',
        'document_title' => 'required|max_length[255]',
        'file_path' => 'required',
        'submitted_by' => 'required|numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getByOrganization($organizationId, $filters = [])
    {
        $builder = $this->select('document_submissions.*, users.username as submitted_by_name, organizations.name as org_name')
            ->join('users', 'users.id = document_submissions.submitted_by')
            ->join('organizations', 'organizations.id = document_submissions.organization_id')
            ->where('document_submissions.organization_id', $organizationId);

        if (isset($filters['document_type'])) {
            $builder->where('document_submissions.document_type', $filters['document_type']);
        }

        if (isset($filters['academic_year'])) {
            $builder->where('document_submissions.academic_year', $filters['academic_year']);
        }

        if (isset($filters['status'])) {
            $builder->where('document_submissions.status', $filters['status']);
        }

        return $builder->orderBy('document_submissions.created_at', 'DESC')->findAll();
    }

    public function getAllSubmissions($filters = [])
    {
        $builder = $this->select('document_submissions.*, users.username as submitted_by_name, organizations.name as org_name')
            ->join('users', 'users.id = document_submissions.submitted_by')
            ->join('organizations', 'organizations.id = document_submissions.organization_id');

        if (isset($filters['organization_id'])) {
            $builder->where('document_submissions.organization_id', $filters['organization_id']);
        }

        if (isset($filters['document_type'])) {
            $builder->where('document_submissions.document_type', $filters['document_type']);
        }

        if (isset($filters['status'])) {
            $builder->where('document_submissions.status', $filters['status']);
        }

        if (isset($filters['campus'])) {
            $builder->where('document_submissions.campus', $filters['campus']);
        }

        if (isset($filters['academic_year'])) {
            $builder->where('document_submissions.academic_year', $filters['academic_year']);
        }

        return $builder->orderBy('document_submissions.created_at', 'DESC')->findAll();
    }

    public function getDocumentWithComments($id)
    {
        $document = $this->select('document_submissions.*, users.username as submitted_by_name, organizations.name as org_name')
            ->join('users', 'users.id = document_submissions.submitted_by')
            ->join('organizations', 'organizations.id = document_submissions.organization_id')
            ->find($id);

        if ($document) {
            $commentModel = new \App\Models\CommentModel();
            $document['comments'] = $commentModel->getByDocument($id);
        }

        return $document;
    }

    public function getApprovedYearsByType($organizationId, $documentType)
    {
        return $this->select('document_submissions.academic_year')
            ->where('document_submissions.organization_id', $organizationId)
            ->where('document_submissions.document_type', $documentType)
            ->where('document_submissions.status', 'approved')
            ->groupBy('document_submissions.academic_year')
            ->orderBy('document_submissions.academic_year', 'DESC')
            ->findColumn('academic_year');
    }

    public function getApprovedByOrganizationAndType($organizationId, $documentType)
    {
        return $this->select('document_submissions.*, users.username as submitted_by_name')
            ->join('users', 'users.id = document_submissions.submitted_by')
            ->where('document_submissions.organization_id', $organizationId)
            ->where('document_submissions.document_type', $documentType)
            ->where('document_submissions.status', 'approved')
            ->orderBy('document_submissions.academic_year', 'DESC')
            ->orderBy('document_submissions.created_at', 'DESC')
            ->findAll();
    }

    public function getCountByAcademicYear($academicYear)
    {
        return $this->where('academic_year', $academicYear)->countAllResults();
    }
}