<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationChecklistModel extends Model
{
    protected $table = 'organization_checklists';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'organization_id',
        'academic_year',
        'application_letter',
        'officer_list',
        'commitment_forms',
        'constitution_bylaws',
        'org_structure',
        'calendar_activities',
        'financial_report',
        'program_expenditures',
        'accomplishment_report'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getByOrgAndYear($organizationId, $academicYear)
    {
        return $this->where('organization_id', $organizationId)
            ->where('academic_year', $academicYear)
            ->first();
    }

    public function isComplete($checklist)
    {
        if (!$checklist)
            return false;

        $requiredFields = [
            'application_letter',
            'officer_list',
            'commitment_forms',
            'constitution_bylaws',
            'org_structure',
            'calendar_activities',
            'financial_report',
            'program_expenditures',
            'accomplishment_report'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($checklist[$field]) || $checklist[$field] != 1) {
                return false;
            }
        }

        return true;
    }
}
