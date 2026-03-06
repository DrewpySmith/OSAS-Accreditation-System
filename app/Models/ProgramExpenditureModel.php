<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramExpenditureModel extends Model
{
    protected $table = 'program_expenditures';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'organization_id',
        'academic_year',
        'fee_type',
        'amount',
        'frequency',
        'number_of_students',
        'total'
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
        'fee_type' => 'required|max_length[255]',
        'amount' => 'required|decimal',
        'frequency' => 'required|max_length[100]',
        'number_of_students' => 'required|numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    protected $beforeInsert = ['calculateTotal'];
    protected $beforeUpdate = ['calculateTotal'];

    protected function calculateTotal(array $data)
    {
        if (isset($data['data']['amount']) && isset($data['data']['number_of_students'])) {
            $data['data']['total'] = $data['data']['amount'] * $data['data']['number_of_students'];
        }
        return $data;
    }

    public function getByOrganizationAndYear($organizationId, $academicYear)
    {
        return $this->where('organization_id', $organizationId)
            ->where('academic_year', $academicYear)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function getTotalByYear($organizationId, $academicYear)
    {
        $result = $this->selectSum('total')
            ->where('organization_id', $organizationId)
            ->where('academic_year', $academicYear)
            ->first();
        
        return $result['total'] ?? 0;
    }

    public function getYearlySummary($organizationId)
    {
        return $this->select('academic_year, SUM(total) as grand_total')
            ->where('organization_id', $organizationId)
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'DESC')
            ->findAll();
    }
}