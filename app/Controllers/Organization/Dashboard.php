<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\OrganizationModel;
use App\Models\DocumentSubmissionModel;
use App\Models\CalendarActivityModel;
use App\Models\FinancialReportModel;
use App\Models\NotificationModel;
use App\Models\OrganizationChecklistModel;
use Config\Database;

class Dashboard extends BaseController
{
    protected $organizationModel;
    protected $documentModel;
    protected $calendarModel;
    protected $financialModel;
    protected $notificationModel;
    protected $checklistModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
        $this->documentModel = new DocumentSubmissionModel();
        $this->calendarModel = new CalendarActivityModel();
        $this->financialModel = new FinancialReportModel();
        $this->notificationModel = new NotificationModel();
        $this->checklistModel = new OrganizationChecklistModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'organization') {
            return redirect()->to('/admin/dashboard');
        }

        $organizationId = session()->get('organization_id');
        $userId = session()->get('user_id');

        $db = Database::connect();
        $currentAY = $db->table('academic_years')->where('is_current', 1)->get()->getRowArray();
        $academicYear = $currentAY ? $currentAY['year'] : date('Y') . '-' . (date('Y') + 1);

        $data = [
            'organization' => $this->organizationModel->find($organizationId),
            'total_documents' => $this->documentModel->where('organization_id', $organizationId)->countAllResults(),
            'pending_reviews' => $this->documentModel->where('organization_id', $organizationId)
                ->where('status', 'pending')
                ->countAllResults(),
            'upcoming_activities' => $this->calendarModel->where('organization_id', $organizationId)
                ->where('activity_date >=', date('Y-m-d'))
                ->countAllResults(),
            'reports_this_year' => $this->financialModel->where('organization_id', $organizationId)
                ->where('academic_year', date('Y') . '-' . (date('Y') + 1))
                ->countAllResults(),
            'unread_count' => $this->notificationModel->getUnreadCount($userId),
            'academic_year' => $academicYear,
            'checklist' => $this->checklistModel->getByOrgAndYear($organizationId, $academicYear),
            'document_types' => \App\Models\DocumentSubmissionModel::DOCUMENT_TYPES
        ];

        return view('organization/dashboard', $data);
    }
}