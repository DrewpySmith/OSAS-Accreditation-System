<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FinancialReportModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class FinancialReports extends BaseController
{
    protected $financialModel;

    public function __construct()
    {
        $this->financialModel = new FinancialReportModel();
    }

    public function download($id)
    {
        $report = $this->financialModel->find($id);
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

        return $dompdf->stream('financial_report_' . ($report['academic_year'] ?? $id) . '.pdf', ['Attachment' => true]);
    }

    public function print($id)
    {
        $report = $this->financialModel->find($id);
        if (!$report) {
            return redirect()->back()->with('error', 'Report not found');
        }

        $report['collections'] = json_decode($report['collections'], true);
        $report['expenses'] = json_decode($report['expenses'], true);

        return view('pdf/financial_report', ['report' => $report]);
    }
}
