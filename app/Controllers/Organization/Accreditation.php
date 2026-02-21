<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\OrganizationModel;
use App\Models\OrganizationChecklistModel;
use Config\Database;
use Dompdf\Dompdf;
use Dompdf\Options;

class Accreditation extends BaseController
{
    protected $organizationModel;
    protected $checklistModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
        $this->checklistModel = new OrganizationChecklistModel();
    }

    public function index()
    {
        $organizationId = session()->get('organization_id');

        $db = Database::connect();
        $currentAY = $db->table('academic_years')->where('is_current', 1)->get()->getRowArray();
        $academicYear = $currentAY ? $currentAY['year'] : date('Y') . '-' . (date('Y') + 1);

        $organization = $this->organizationModel->find($organizationId);
        $checklist = $this->checklistModel->getByOrgAndYear($organizationId, $academicYear);

        // Calculate Progress
        $progress = $this->checklistModel->calculateProgress($checklist);

        $data = [
            'organization' => $organization,
            'checklist' => $checklist,
            'academic_year' => $academicYear,
            'progress' => $progress,
            'is_complete' => $this->checklistModel->isComplete($checklist),
            'document_types' => \App\Models\DocumentSubmissionModel::DOCUMENT_TYPES
        ];

        return view('organization/accreditation/index', $data);
    }

    public function download($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $checklist = $this->checklistModel->getByOrgAndYear($organizationId, $academicYear);

        if (!$this->checklistModel->isComplete($checklist)) {
            return redirect()->back()->with('error', 'Accreditation incomplete.');
        }

        $organization = $this->organizationModel->find($organizationId);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('pdf/certificate', [
            'organization' => $organization,
            'academic_year' => $academicYear,
            'date_issued' => date('F d, Y')
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->stream('Certificate_of_Accreditation_' . $organization['acronym'] . '.pdf', ['Attachment' => true]);
    }

    public function print($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $checklist = $this->checklistModel->getByOrgAndYear($organizationId, $academicYear);

        if (!$this->checklistModel->isComplete($checklist)) {
            echo "Accreditation incomplete.";
            return;
        }

        $organization = $this->organizationModel->find($organizationId);

        return view('pdf/certificate', [
            'organization' => $organization,
            'academic_year' => $academicYear,
            'date_issued' => date('F d, Y')
        ]);
    }
}
