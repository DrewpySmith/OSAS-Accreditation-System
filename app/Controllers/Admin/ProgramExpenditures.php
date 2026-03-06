<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProgramExpenditureModel;
use App\Models\OrganizationModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ProgramExpenditures extends BaseController
{
    protected $expenditureModel;
    protected $organizationModel;

    public function __construct()
    {
        $this->expenditureModel = new ProgramExpenditureModel();
        $this->organizationModel = new OrganizationModel();
    }

    public function download($organizationId, $academicYear)
    {
        $organization = $this->organizationModel->find($organizationId);
        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found');
        }

        $expenditures = $this->expenditureModel->getByOrganizationAndYear($organizationId, $academicYear);
        $grandTotal = $this->expenditureModel->getTotalByYear($organizationId, $academicYear);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('pdf/program_expenditure', [
            'expenditures' => $expenditures,
            'organization' => $organization,
            'academic_year' => $academicYear,
            'grand_total' => $grandTotal
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('program_expenditure_' . $organizationId . '_' . $academicYear . '.pdf', ['Attachment' => true]);
    }

    public function print($organizationId, $academicYear)
    {
        $organization = $this->organizationModel->find($organizationId);
        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found');
        }

        $data['expenditures'] = $this->expenditureModel->getByOrganizationAndYear($organizationId, $academicYear);
        $data['organization'] = $organization;
        $data['academic_year'] = $academicYear;
        $data['grand_total'] = $this->expenditureModel->getTotalByYear($organizationId, $academicYear);

        return view('pdf/program_expenditure', $data);
    }
}
