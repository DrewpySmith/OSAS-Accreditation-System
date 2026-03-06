<?php

namespace App\Models;

use CodeIgniter\Model;

class AccomplishmentReportModel extends Model
{
    protected $table = 'accomplishment_reports';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'organization_id',
        'academic_year',
        'activity_title',
        'narrative_report',
        'pictorials',
        'activity_designs',
        'evaluation_sheets',
        'status'
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
        'academic_year' => 'required|max_length[20]',
        'activity_title' => 'required|max_length[255]',
        'narrative_report' => 'required',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getByOrganization($organizationId, $academicYear = null)
    {
        $builder = $this->where('organization_id', $organizationId);
        
        if ($academicYear) {
            $builder->where('academic_year', $academicYear);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function getWithFiles($id)
    {
        $report = $this->find($id);
        
        if ($report) {
            // Decode JSON fields
            $report['pictorials'] = json_decode($report['pictorials'], true) ?? [];
            $report['activity_designs'] = json_decode($report['activity_designs'], true) ?? [];
            $report['evaluation_sheets'] = json_decode($report['evaluation_sheets'], true) ?? [];
        }
        
        return $report;
    }

    public function addFile($reportId, $fileType, $filePath)
    {
        $report = $this->find($reportId);
        if (!$report) {
            return false;
        }

        $files = json_decode($report[$fileType], true) ?? [];
        $files[] = $filePath;

        return $this->update($reportId, [
            $fileType => json_encode($files)
        ]);
    }

    public function removeFile($reportId, $fileType, $fileName)
    {
        $report = $this->find($reportId);
        if (!$report) {
            return false;
        }

        $files = json_decode($report[$fileType], true) ?? [];
        $files = array_filter($files, function($file) use ($fileName) {
            return basename($file) !== $fileName;
        });

        return $this->update($reportId, [
            $fileType => json_encode(array_values($files))
        ]);
    }
}