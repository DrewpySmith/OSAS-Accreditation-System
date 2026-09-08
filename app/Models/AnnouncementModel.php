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
        'priority',
        'status',
        'is_pinned',
        'publish_at',
        'expires_at',
        'action_label',
        'action_url',
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

    public const PRIORITIES = ['normal', 'urgent', 'critical'];
    public const STATUSES = ['draft', 'published', 'expired', 'archived'];

    public function getForOrg($orgId, $campus = null, $limit = null, $filters = [])
    {
        $builder = $this->distinct()->select('announcements.*')
            ->join('announcement_targets', 'announcement_targets.announcement_id = announcements.id', 'left')
            ->where('announcements.status', 'published')
            ->where('announcements.is_active', 1)
            ->groupStart()
                ->where('announcements.target_type', 'all')
                ->orWhere('announcement_targets.target_type', 'all')
                ->orWhere('announcement_targets.target_value', $orgId)
                ->orWhere('announcements.target_value', $orgId)
            ->groupEnd();

        if ($campus) {
            $builder->orGroupStart()
                ->where('announcement_targets.target_type', 'campus')
                ->where('announcement_targets.target_value', $campus)
                ->orGroupStart()
                    ->where('announcements.target_type', 'campus')
                    ->where('announcements.target_value', $campus)
                ->groupEnd()
            ->groupEnd();
        }

        $now = date('Y-m-d H:i:s');
        $builder->groupStart()
            ->where('announcements.publish_at IS NULL', null, false)
            ->orWhere('announcements.publish_at <=', $now)
        ->groupEnd();
        $builder->groupStart()
            ->where('announcements.expires_at IS NULL', null, false)
            ->orWhere('announcements.expires_at >', $now)
        ->groupEnd();

        if (!empty($filters['priority'])) {
            $builder->where('announcements.priority', $filters['priority']);
        }
        if (!empty($filters['search'])) {
            $builder->like('announcements.title', $filters['search']);
        }
        if (!empty($filters['ack']) && isset($filters['orgId'])) {
            $db = \Config\Database::connect();
            $sub = $db->table('announcement_reads')->select('announcement_id')->where('organization_id', $filters['orgId'])->where('acknowledged_at IS NOT NULL', null, false);
            if ($filters['ack'] === 'acknowledged') {
                $builder->where('announcements.id IN (' . $sub->getCompiledSelect() . ')', null, false);
            } elseif ($filters['ack'] === 'pending') {
                $builder->where('announcements.id NOT IN (' . $sub->getCompiledSelect() . ')', null, false);
            }
        }

        $builder->orderBy('announcements.is_pinned', 'DESC');
        $builder->orderBy("CASE announcements.priority WHEN 'critical' THEN 3 WHEN 'urgent' THEN 2 ELSE 1 END", 'DESC', false);
        $builder->orderBy('announcements.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function getUnreadCount($orgId, $campus = null)
    {
        $ids = array_column($this->getForOrg($orgId, $campus), 'id');
        if (empty($ids)) return 0;
        $db = \Config\Database::connect();
        $acked = $db->table('announcement_reads')->select('announcement_id')->where('organization_id', $orgId)->where('acknowledged_at IS NOT NULL', null, false)->whereIn('announcement_id', $ids)->get()->getResultArray();
        $ackedIds = array_column($acked, 'announcement_id');
        return count(array_diff($ids, $ackedIds));
    }

    public function getTargets($announcementId)
    {
        return model(AnnouncementTargetModel::class)->where('announcement_id', $announcementId)->findAll();
    }

    public function getAttachments($announcementId)
    {
        return model(AnnouncementAttachmentModel::class)->where('announcement_id', $announcementId)->findAll();
    }

    public function getCompliance($announcementId, $campus = null, $orgIds = null)
    {
        $targets = $this->getTargets($announcementId);
        $ann = $this->find($announcementId);
        $orgModel = model(\App\Models\OrganizationModel::class);
        $builder = $orgModel->where('status', 'active');
        if (!empty($orgIds)) {
            $builder->whereIn('id', $orgIds);
        } elseif (!empty($targets)) {
            $hasAll = false;
            $campuses = [];
            $specificOrgs = [];
            foreach ($targets as $t) {
                if ($t['target_type'] === 'all' || $ann['target_type'] === 'all') $hasAll = true;
                if ($t['target_type'] === 'campus') $campuses[] = $t['target_value'];
                if ($t['target_type'] === 'organization') $specificOrgs[] = $t['target_value'];
            }
            if ($ann['target_type'] === 'campus') $campuses[] = $ann['target_value'];
            if ($ann['target_type'] === 'organization') $specificOrgs[] = $ann['target_value'];
            if (!$hasAll) {
                $builder->groupStart();
                if (!empty($campuses)) $builder->orWhereIn('campus', $campuses);
                if (!empty($specificOrgs)) $builder->orWhereIn('id', $specificOrgs);
                $builder->groupEnd();
                if (empty($campuses) && empty($specificOrgs)) $builder->where('1=0');
            }
        } else {
            if ($ann['target_type'] === 'campus') $builder->where('campus', $ann['target_value']);
            elseif ($ann['target_type'] === 'organization') $builder->where('id', $ann['target_value']);
        }
        if ($campus) $builder->where('campus', $campus);
        $orgs = $builder->findAll();
        $db = \Config\Database::connect();
        $ackedRows = $db->table('announcement_reads')->where('announcement_id', $announcementId)->where('acknowledged_at IS NOT NULL', null, false)->get()->getResultArray();
        $ackedIds = array_column($ackedRows, 'organization_id');
        $pending = [];
        $acked = [];
        foreach ($orgs as $o) {
            if (in_array($o['id'], $ackedIds)) $acked[] = $o;
            else $pending[] = $o;
        }
        return ['total' => count($orgs), 'acked' => $acked, 'pending' => $pending, 'ackedCount' => count($acked), 'pendingCount' => count($pending)];
    }
}
