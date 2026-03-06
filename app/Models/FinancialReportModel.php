<?php

namespace App\Models;

use CodeIgniter\Model;

class FinancialReportModel extends Model
{
    protected $table = 'financial_reports';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'organization_id',
        'academic_year',
        'collections',
        'expenses',
        'total_collection',
        'total_expenses',
        'cash_on_bank',
        'cash_on_hand',
        'total_remaining_fund',
        'passbook_copy',
        'treasurer_name',
        'auditor_name',
        'head_name',
        'adviser_name',
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
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getByOrganizationAndYear($organizationId, $academicYear)
    {
        return $this->where('organization_id', $organizationId)
            ->where('academic_year', $academicYear)
            ->first();
    }

    public function getYearlyComparison($organizationId, $years = [])
    {
        $builder = $this->where('organization_id', $organizationId);
        
        if (!empty($years)) {
            $builder->whereIn('academic_year', $years);
        }
        
        return $builder->orderBy('academic_year', 'DESC')->findAll();
    }

    public function getFinancialSummary($organizationId, $academicYear)
    {
        $report = $this->getByOrganizationAndYear($organizationId, $academicYear);
        
        if (!$report) {
            return null;
        }

        return [
            'total_collection' => $report['total_collection'],
            'total_expenses' => $report['total_expenses'],
            'cash_on_bank' => $report['cash_on_bank'],
            'cash_on_hand' => $report['cash_on_hand'],
            'total_remaining_fund' => $report['total_remaining_fund'],
            'collections' => json_decode($report['collections'], true),
            'expenses' => json_decode($report['expenses'], true),
        ];
    }

    public function getAllYears($organizationId)
    {
        return $this->select('academic_year')
            ->where('organization_id', $organizationId)
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'DESC')
            ->findColumn('academic_year');
    }

    public function getAllDistinctYears()
    {
        return $this->select('academic_year')
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'DESC')
            ->findColumn('academic_year');
    }
}