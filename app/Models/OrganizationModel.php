<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationModel extends Model
{
    protected $table = 'organizations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'acronym',
        'campus',
        'description',
        'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public const CAMPUSES = [
        'Palimbang',
        'Isulan',
        'Access',
        'Tacurong',
        'Lutayan',
        'Bagumbayan',
        'Kalamansig'
    ];

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'campus' => 'required|in_list[Palimbang,Isulan,Access,Tacurong,Lutayan,Bagumbayan,Kalamansig]',
        'status' => 'required|in_list[active,inactive,suspended]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getActiveOrganizations()
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getOrganizationWithStats($id)
    {
        $org = $this->find($id);
        if (!$org) {
            return null;
        }

        // Get statistics
        $db = \Config\Database::connect();

        $org['total_documents'] = $db->table('document_submissions')
            ->where('organization_id', $id)
            ->countAllResults();

        $org['total_activities'] = $db->table('calendar_activities')
            ->where('organization_id', $id)
            ->countAllResults();

        $org['total_financial_reports'] = $db->table('financial_reports')
            ->where('organization_id', $id)
            ->countAllResults();

        return $org;
    }
}