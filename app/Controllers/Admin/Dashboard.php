<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrganizationModel;
use App\Models\DocumentSubmissionModel;
use App\Models\FinancialReportModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $organizationModel;
    protected $documentModel;
    protected $financialModel;
    protected $userModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
        $this->documentModel = new DocumentSubmissionModel();
        $this->financialModel = new FinancialReportModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/organization/dashboard');
        }

        // Get monthly stats for the chart
        $db = \Config\Database::connect();
        $monthlyStats = $db->table('document_submissions')
            ->select("DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count")
            ->groupBy("month")
            ->orderBy("created_at", "ASC")
            ->limit(10)
            ->get()
            ->getResultArray();

        $data = [
            'total_organizations' => $this->organizationModel->where('status', 'active')->countAllResults(),
            'total_documents' => $this->documentModel->countAllResults(),
            'pending_documents' => $this->documentModel->where('status', 'pending')->countAllResults(),
            'recent_submissions' => $this->documentModel->getAllSubmissions(),
            'organizations' => $this->organizationModel->findAll(),
            'monthly_stats' => $monthlyStats
        ];

        // Format chart labels and values
        $data['chart_labels'] = array_column($monthlyStats, 'month');
        $data['chart_data'] = array_column($monthlyStats, 'count');

        // Limit recent submissions
        $data['recent_submissions'] = array_slice($data['recent_submissions'], 0, 10);

        return view('admin/dashboard_modern', $data);
    }
}