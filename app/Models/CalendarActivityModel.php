<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarActivityModel extends Model
{
    protected $table = 'calendar_activities';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'organization_id',
        'academic_year',
        'activity_date',
        'activity_title',
        'responsible_person',
        'remarks',
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
        'activity_date' => 'required|valid_date',
        'activity_title' => 'required|max_length[255]',
        'responsible_person' => 'required|max_length[255]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function getByOrganizationAndYear($organizationId, $academicYear)
    {
        return $this->where('organization_id', $organizationId)
            ->where('academic_year', $academicYear)
            ->orderBy('activity_date', 'ASC')
            ->findAll();
    }

    public function getByOrganization($organizationId)
    {
        return $this->where('organization_id', $organizationId)
            ->orderBy('activity_date', 'DESC')
            ->findAll();
    }

    public function getAllYears($organizationId)
    {
        $rows = $this->select('academic_year')
            ->where('organization_id', $organizationId)
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'DESC')
            ->findAll();

        return array_values(array_filter(array_map(static function ($row) {
            return $row['academic_year'] ?? null;
        }, $rows)));
    }

    public function getUpcomingActivities($organizationId, $limit = 5)
    {
        return $this->where('organization_id', $organizationId)
            ->where('activity_date >=', date('Y-m-d'))
            ->where('status !=', 'cancelled')
            ->orderBy('activity_date', 'ASC')
            ->limit($limit)
            ->findAll();
    }
}