<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'title',
        'message',
        'sender_id',
        'target_type',
        'target_value',
        'is_active',
        'summary',
        'content',
        'priority',
        'category',
        'author',
        'is_pinned',
        'expires_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|max_length[255]',
        'message' => 'required',
        'sender_id' => 'required|numeric',
        'target_type' => 'required|in_list[all,campus,organization]',
    ];

    public function getForOrg($orgId, $campus = null, $limit = null)
    {
        $builder = $this->where('is_active', 1)
            ->groupStart()
                ->where('target_type', 'all')
                ->orWhere('target_value', $orgId)
                ->orWhere('target_type', 'organization')
            ->groupEnd();

        if ($campus) {
            $builder->orGroupStart()
                ->where('target_type', 'campus')
                ->where('target_value', $campus)
            ->groupEnd();
        }

        // Exclude expired announcements
        $builder->groupStart()
            ->where('expires_at IS NULL', null, false)
            ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
        ->groupEnd();

        $builder->orderBy('is_pinned', 'DESC');
        $builder->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function getUnreadCount($orgId, $campus = null)
    {
        $db = \Config\Database::connect();
        $subQuery = $db->table('announcement_reads')
            ->select('announcement_id')
            ->where('organization_id', $orgId);

        $builder = $this->where('is_active', 1)
            ->where('id NOT IN (' . $subQuery->getCompiledSelect() . ')', null, false)
            ->groupStart()
                ->where('target_type', 'all')
                ->orWhere('target_value', $orgId)
                ->orWhere('target_type', 'organization')
            ->groupEnd();

        if ($campus) {
            $builder->orGroupStart()
                ->where('target_type', 'campus')
                ->where('target_value', $campus)
            ->groupEnd();
        }

        return $builder->countAllResults();
    }

    public function togglePin($id)
    {
        $announcement = $this->find($id);
        if (!$announcement) {
            return false;
        }

        return $this->update($id, [
            'is_pinned' => $announcement['is_pinned'] ? 0 : 1,
        ]);
    }
}
