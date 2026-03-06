<?php

namespace App\Models;

use CodeIgniter\Model;

class CommitmentFormModel extends Model
{
    protected $table = 'commitment_forms';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'organization_id',
        'officer_name',
        'position',
        'organization_name',
        'academic_year',
        'signed_date',
        'signature',
        'status',
        'sort_order'
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
        'officer_name' => 'required|min_length[3]|max_length[255]',
        'position' => 'required|max_length[100]',
        'organization_name' => 'required|max_length[255]',
        'academic_year' => 'required|max_length[20]',
        'signed_date' => 'required|valid_date',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getByOrganization($organizationId, $academicYear = null)
    {
        $builder = $this->where('organization_id', $organizationId);

        if ($academicYear) {
            $builder->where('academic_year', $academicYear);
        }

        return $builder->orderBy('sort_order', 'ASC')->orderBy('created_at', 'DESC')->findAll();
    }

    public function getByOrganizationAndYear($organizationId, $academicYear)
    {
        return $this->where('organization_id', $organizationId)
            ->where('academic_year', $academicYear)
            ->first();
    }
}