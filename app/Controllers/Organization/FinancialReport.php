<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\FinancialReportModel;
use App\Models\OrganizationModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class FinancialReport extends BaseController
{
    protected $financialModel;
    protected $organizationModel;

    public function __construct()
    {
        $this->financialModel = new FinancialReportModel();
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        $organizationId = session()->get('organization_id');
        $data['years'] = $this->financialModel->getAllYears($organizationId);
        $data['organization'] = $this->organizationModel->find($organizationId);

        $selectedYear = $this->request->getGet('year');
        if (empty($selectedYear) && !empty($data['years'])) {
            $selectedYear = $data['years'][0];
        }

        $data['report'] = null;
        if (!empty($selectedYear)) {
            $report = $this->financialModel->getByOrganizationAndYear($organizationId, $selectedYear);
            if ($report) {
                $report['collections'] = json_decode($report['collections'], true) ?: [];
                $report['expenses'] = json_decode($report['expenses'], true) ?: [];
                $data['report'] = $report;
            }
        }

        return view('organization/forms/financial_report', $data);
    }

    public function getReport($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $report = $this->financialModel->getByOrganizationAndYear($organizationId, $academicYear);

        if ($report) {
            $report['collections'] = json_decode($report['collections'], true);
            $report['expenses'] = json_decode($report['expenses'], true);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $report,
            'csrf' => csrf_hash()
        ]);
    }

    public function store()
    {
        $organizationId = session()->get('organization_id');
        $academicYear = $this->request->getPost('academic_year');

        // Check if report already exists
        $existing = $this->financialModel->getByOrganizationAndYear($organizationId, $academicYear);

        $collections = $this->request->getPost('collections');
        $expenses = $this->request->getPost('expenses');

        if (is_string($collections)) {
            $decoded = json_decode($collections, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $collections = $decoded;
            }
        }

        if (is_string($expenses)) {
            $decoded = json_decode($expenses, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $expenses = $decoded;
            }
        }

        $totalCollection = 0;
        if (is_array($collections)) {
            foreach ($collections as $collection) {
                $totalCollection += floatval($collection['amount'] ?? 0);
            }
        }

        $totalExpenses = 0;
        if (is_array($expenses)) {
            foreach ($expenses as $expense) {
                $totalExpenses += floatval($expense['amount'] ?? 0);
            }
        }

        $cashOnBank = floatval($this->request->getPost('cash_on_bank'));
        $cashOnHand = $totalCollection - $totalExpenses - $cashOnBank;
        $totalRemainingFund = $cashOnBank + $cashOnHand;

        $data = [
            'organization_id' => $organizationId,
            'academic_year' => $academicYear,
            'collections' => json_encode($collections),
            'expenses' => json_encode($expenses),
            'total_collection' => $totalCollection,
            'total_expenses' => $totalExpenses,
            'cash_on_bank' => $cashOnBank,
            'cash_on_hand' => $cashOnHand,
            'total_remaining_fund' => $totalRemainingFund,
            'treasurer_name' => $this->request->getPost('treasurer_name'),
            'auditor_name' => $this->request->getPost('auditor_name'),
            'head_name' => $this->request->getPost('head_name'),
            'adviser_name' => $this->request->getPost('adviser_name'),
            'status' => 'draft'
        ];

        // Handle passbook upload
        $passbook = $this->request->getFile('passbook_copy');
        if ($passbook && $passbook->isValid()) {
            $newName = $passbook->getRandomName();
            $passbook->move(WRITEPATH . 'uploads/passbooks/' . $organizationId, $newName);
            $data['passbook_copy'] = 'passbooks/' . $organizationId . '/' . $newName;
        }

        if ($existing) {
            $result = $this->financialModel->update($existing['id'], $data);
            $message = 'Financial report updated successfully';
        } else {
            $result = $this->financialModel->insert($data);
            $message = 'Financial report created successfully';
        }

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to save financial report',
            'csrf' => csrf_hash()
        ]);
    }

    public function tracking()
    {
        $organizationId = session()->get('organization_id');
        $years = $this->financialModel->getAllYears($organizationId);

        $data['reports'] = $this->financialModel->getYearlyComparison($organizationId, $years);
        $data['years'] = $years;
        $data['organization'] = $this->organizationModel->find($organizationId);

        return view('organization/reports/financial_tracking', $data);
    }

    public function comparison()
    {
        $organizationId = session()->get('organization_id');
        $payload = $this->request->getJSON(true);
        if (!is_array($payload) || empty($payload)) {
            $payload = $this->request->getPost();
        }
        $selectedYears = $payload['years'] ?? $this->request->getPost('years');

        if (!$selectedYears || !is_array($selectedYears)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select at least one year',
                'csrf' => csrf_hash()
            ]);
        }

        $reports = $this->financialModel->getYearlyComparison($organizationId, $selectedYears);

        $comparisonData = [];
        foreach ($reports as $report) {
            $comparisonData[] = [
                'year' => $report['academic_year'],
                'total_collection' => $report['total_collection'],
                'total_expenses' => $report['total_expenses'],
                'remaining_fund' => $report['total_remaining_fund']
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $comparisonData,
            'csrf' => csrf_hash()
        ]);
    }

    public function download($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $report = $this->financialModel->getByOrganizationAndYear($organizationId, $academicYear);

        if (!$report) {
            return redirect()->back()->with('error', 'Report not found');
        }

        $report['collections'] = json_decode($report['collections'], true);
        $report['expenses'] = json_decode($report['expenses'], true);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('pdf/financial_report', ['report' => $report]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('financial_report_' . $report['academic_year'] . '.pdf', ['Attachment' => true]);
    }

    public function print($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $report = $this->financialModel->getByOrganizationAndYear($organizationId, $academicYear);

        if (!$report) {
            return redirect()->back()->with('error', 'Report not found');
        }

        $report['collections'] = json_decode($report['collections'], true);
        $report['expenses'] = json_decode($report['expenses'], true);

        return view('pdf/financial_report', ['report' => $report]);
    }
}