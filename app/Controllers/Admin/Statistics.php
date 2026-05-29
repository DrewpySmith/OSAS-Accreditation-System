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

    public function organizationData($id)
    {
        $organization = $this->organizationModel->find($id);
        
        if (!$organization) {
            return $this->response->setJSON(['success' => false, 'message' => 'Organization not found', 'csrf' => csrf_hash()]);
        }

        $years         = $this->financialModel->getAllYears($id);
        $calendarYears = $this->calendarActivityModel->getAllYears($id);
        $firstYear     = !empty($years) ? $years[0] : null;
        $calFirstYear  = !empty($calendarYears) ? $calendarYears[0] : null;

        $commitmentForms = $this->commitmentModel->getByOrganization($id);
        $financialReports = $this->financialModel->getYearlyComparison($id);
        $expenditureSummary = $this->expenditureModel->getYearlySummary($id);
        $approvedFinancialDocs = $this->documentModel->getApprovedByOrganizationAndType($id, 'financial_report');

        // Format dates for JSON output
        $formattedCF = array_map(function($cf) {
            $cf['signed_date_formatted'] = !empty($cf['signed_date']) ? date('M d, Y', strtotime($cf['signed_date'])) : '';
            return $cf;
        }, $commitmentForms);

        $formattedDocs = array_map(function($doc) {
            $doc['created_at_formatted'] = !empty($doc['created_at']) ? date('M d, Y', strtotime($doc['created_at'])) : '';
            return $doc;
        }, $approvedFinancialDocs);

        return $this->response->setJSON([
            'success'           => true,
            'organization'      => $organization,
            'years'             => $years,
            'calendar_years'    => $calendarYears,
            'first_year'        => $firstYear,
            'cal_first_year'    => $calFirstYear,
            'commitment_forms'  => $formattedCF,
            'financial_reports' => $financialReports,
            'expenditure_summary' => $expenditureSummary,
            'approved_financial_report_documents' => $formattedDocs,
            'csrf'              => csrf_hash(),
        ]);
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

    /**
     * Fetch detailed accreditation statistics filtered by campus
     */
    public function getDashboardData()
    {
        $campus = $this->request->getGet('campus');
        
        $db = \Config\Database::connect();
        
        // Build base organization query
        $orgQuery = $this->organizationModel;
        if (!empty($campus) && $campus !== 'all') {
            $orgQuery = $orgQuery->where('campus', $campus);
        }
        $organizations = $orgQuery->findAll();

        $ayRow = $db->table('academic_years')->where('is_current', 1)->get()->getRowArray();
        $academicYear = $ayRow ? $ayRow['year'] : '2024-2025';

        $orgList = [];
        $accreditedCount = 0;
        $unaccreditedCount = 0;

        $requiredFields = [
            'application_letter' => 'Application Letter Form',
            'officer_list' => 'Lists of Officers',
            'commitment_forms' => 'Commitment Forms of Officers and Advisers',
            'constitution_bylaws' => 'Constitution and By-Laws',
            'org_structure' => 'Organizational Structure',
            'calendar_activities' => 'Plan and Calendar of Activities',
            'financial_report' => 'Audited Financial Report',
            'program_expenditures' => 'Program of Expenditures',
            'accomplishment_report' => 'Accomplishment Reports'
        ];

        foreach ($organizations as $org) {
            // Find current checklist for organization
            $checklist = $db->table('organization_checklists')
                            ->where('organization_id', $org['id'])
                            ->where('academic_year', $academicYear)
                            ->get()
                            ->getRowArray();

            $verifiedCount = 0;
            $missing = [];

            foreach ($requiredFields as $field => $label) {
                if ($checklist && isset($checklist[$field]) && (int)$checklist[$field] === 1) {
                    $verifiedCount++;
                } else {
                    $missing[] = $label;
                }
            }

            // Calculate precise percentage based on the 9 items
            $progress = round(($verifiedCount / 9) * 100);

            if ($progress === 100.0) {
                $accreditedCount++;
            } else {
                $unaccreditedCount++;
            }

            $orgList[] = [
                'id'                   => $org['id'],
                'name'                 => $org['name'],
                'acronym'              => $org['acronym'] ?: 'N/A',
                'campus'               => $org['campus'],
                'progress'             => $progress,
                'missing_requirements' => $missing,
                'status'               => $org['status']
            ];
        }

        return $this->response->setJSON([
            'success'            => true,
            'selected_campus'    => $campus ?: 'all',
            'total_orgs'         => count($orgList),
            'accredited_count'   => $accreditedCount,
            'unaccredited_count' => $unaccreditedCount,
            'organizations'      => $orgList
        ]);
    }
}