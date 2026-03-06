<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrganizationModel;
use App\Models\FinancialReportModel;
use App\Models\ProgramExpenditureModel;
use App\Models\DocumentSubmissionModel;
use App\Models\CommitmentFormModel;
use App\Models\CalendarActivityModel;

class Statistics extends BaseController
{
    protected $organizationModel;
    protected $financialModel;
    protected $expenditureModel;
    protected $documentModel;
    protected $commitmentModel;
    protected $calendarActivityModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
        $this->financialModel = new FinancialReportModel();
        $this->expenditureModel = new ProgramExpenditureModel();
        $this->documentModel = new DocumentSubmissionModel();
        $this->commitmentModel = new CommitmentFormModel();
        $this->calendarActivityModel = new CalendarActivityModel();
    }

    public function index()
    {
        $data['organizations'] = $this->organizationModel->findAll();
        $data['years'] = $this->financialModel->getAllDistinctYears();
        
        // Get current school year (assuming current year)
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $currentSchoolYear = $currentYear . '-' . $nextYear;
        
        // Get uploaded docs count for current school year
        $data['uploaded_docs_this_year'] = $this->documentModel->getCountByAcademicYear($currentSchoolYear);
        
        // Get activities per organization
        $data['activities_per_org'] = [];
        foreach ($data['organizations'] as $org) {
            $activities = $this->calendarActivityModel->getByOrganization($org['id']);
            $data['activities_per_org'][] = [
                'name' => $org['name'],
                'count' => count($activities)
            ];
        }
        
        // Get total organizations count
        $data['total_orgs'] = count($data['organizations']);
        
        return view('admin/statistics/index', $data);
    }

    public function organizationView($id)
    {
        $organization = $this->organizationModel->find($id);
        
        if (!$organization) {
            return redirect()->to('/admin/statistics')->with('error', 'Organization not found');
        }

        $data['organization'] = $organization;
        $data['years'] = $this->financialModel->getAllYears($id);
        $data['calendar_years'] = $this->calendarActivityModel->getAllYears($id);
        $data['financial_reports'] = $this->financialModel->getYearlyComparison($id);
        $data['expenditure_summary'] = $this->expenditureModel->getYearlySummary($id);
        $data['commitment_forms'] = $this->commitmentModel->getByOrganization($id);

        $data['approved_financial_report_years'] = $this->documentModel->getApprovedYearsByType($id, 'financial_report');
        $data['approved_financial_report_documents'] = $this->documentModel->getApprovedByOrganizationAndType($id, 'financial_report');
        
        return view('admin/statistics/organization_view', $data);
    }

    public function comparison()
    {
        $payload = $this->request->getJSON(true);
        if (!is_array($payload) || empty($payload)) {
            $payload = $this->request->getPost();
        }

        $organizationIds = $payload['organizations'] ?? $this->request->getPost('organizations');
        $years = $payload['years'] ?? $this->request->getPost('years');

        if (empty($organizationIds) || empty($years)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select organizations and years',
                'csrf' => csrf_hash()
            ]);
        }

        $comparisonData = [];

        foreach ($organizationIds as $orgId) {
            $org = $this->organizationModel->find($orgId);
            $orgData = [
                'id' => $orgId,
                'name' => $org['name'],
                'years' => []
            ];

            foreach ($years as $year) {
                $financial = $this->financialModel->getByOrganizationAndYear($orgId, $year);
                $expenditure = $this->expenditureModel->getTotalByYear($orgId, $year);

                $orgData['years'][$year] = [
                    'collection' => $financial['total_collection'] ?? 0,
                    'expenses' => $financial['total_expenses'] ?? 0,
                    'remaining' => $financial['total_remaining_fund'] ?? 0,
                    'expenditure_budget' => $expenditure
                ];
            }

            $comparisonData[] = $orgData;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $comparisonData,
            'csrf' => csrf_hash()
        ]);
    }

    public function exportData()
    {
        $organizationId = $this->request->getGet('organization_id');
        $year = $this->request->getGet('year');

        if (!$organizationId || !$year) {
            return redirect()->back()->with('error', 'Please select organization and year');
        }

        $organization = $this->organizationModel->find($organizationId);
        $financial = $this->financialModel->getByOrganizationAndYear($organizationId, $year);
        $expenditures = $this->expenditureModel->getByOrganizationAndYear($organizationId, $year);

        $data = [
            'organization' => $organization,
            'financial' => $financial,
            'expenditures' => $expenditures,
            'year' => $year
        ];

        // Simple CSV export
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="statistics_' . $organizationId . '_' . $year . '.csv"');

        $output = fopen('php://output', 'w');
        
        // Financial data
        fputcsv($output, ['Organization', $organization['name']]);
        fputcsv($output, ['Academic Year', $year]);
        fputcsv($output, []);
        fputcsv($output, ['Financial Summary']);
        fputcsv($output, ['Total Collection', $financial['total_collection'] ?? 0]);
        fputcsv($output, ['Total Expenses', $financial['total_expenses'] ?? 0]);
        fputcsv($output, ['Remaining Fund', $financial['total_remaining_fund'] ?? 0]);
        fputcsv($output, []);
        
        // Expenditures
        fputcsv($output, ['Expenditure Details']);
        fputcsv($output, ['Fee Type', 'Amount', 'Frequency', 'Students', 'Total']);
        foreach ($expenditures as $exp) {
            fputcsv($output, [
                $exp['fee_type'],
                $exp['amount'],
                $exp['frequency'],
                $exp['number_of_students'],
                $exp['total']
            ]);
        }

        fclose($output);
        exit;
    }
}