<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AccomplishmentReportModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class AccomplishmentReports extends BaseController
{
    protected $reportModel;

    public function __construct()
    {
        $this->reportModel = new AccomplishmentReportModel();
    }

    public function download($id)
    {
        $report = $this->reportModel->getWithFiles($id);
        if (!$report) {
            return redirect()->back()->with('error', 'Report not found');
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('pdf/accomplishment_report', ['report' => $report]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('accomplishment_report_' . $id . '.pdf', ['Attachment' => true]);
    }

    public function print($id)
    {
        $report = $this->reportModel->getWithFiles($id);
        if (!$report) {
            return redirect()->back()->with('error', 'Report not found');
        }

        return view('pdf/accomplishment_report', ['report' => $report]);
    }
}
