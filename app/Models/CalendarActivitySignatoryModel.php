<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarActivitySignatoryModel extends Model
{
    protected $table = 'calendar_activity_signatories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'organization_id',
        'academic_year',
        'head_name',
        'adviser_name',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getByOrganizationAndYear($organizationId, $academicYear)
    {
        return $this->where('organization_id', $organizationId)
            ->where('academic_year', $academicYear)
            ->first();
    }

    public function upsertByOrganizationAndYear($organizationId, $academicYear, array $data)
    {
        $existing = $this->getByOrganizationAndYear($organizationId, $academicYear);

        $payload = array_merge($data, [
            'organization_id' => $organizationId,
            'academic_year' => $academicYear,
        ]);

        if ($existing) {
            return $this->update($existing['id'], $payload);
        }

        return $this->insert($payload);
    }
}
