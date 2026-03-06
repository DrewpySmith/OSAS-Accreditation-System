<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CalendarActivityModel;
use App\Models\CalendarActivitySignatoryModel;
use App\Models\OrganizationModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class CalendarActivities extends BaseController
{
    protected $activityModel;
    protected $organizationModel;
    protected $signatoryModel;

    public function __construct()
    {
        $this->activityModel = new CalendarActivityModel();
        $this->organizationModel = new OrganizationModel();
        $this->signatoryModel = new CalendarActivitySignatoryModel();
    }

    public function download($organizationId, $academicYear)
    {
        $organization = $this->organizationModel->find($organizationId);
        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found');
        }

        $activities = $this->activityModel->getByOrganizationAndYear($organizationId, $academicYear);
        $signatory = $this->signatoryModel->getByOrganizationAndYear($organizationId, $academicYear);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('pdf/calendar_activities', [
            'activities' => $activities,
            'organization' => $organization,
            'academic_year' => $academicYear,
            'signatory' => $signatory
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('calendar_activities_' . $organizationId . '_' . $academicYear . '.pdf', ['Attachment' => true]);
    }

    public function print($organizationId, $academicYear)
    {
        $organization = $this->organizationModel->find($organizationId);
        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found');
        }

        $data['activities'] = $this->activityModel->getByOrganizationAndYear($organizationId, $academicYear);
        $data['organization'] = $organization;
        $data['academic_year'] = $academicYear;
        $data['signatory'] = $this->signatoryModel->getByOrganizationAndYear($organizationId, $academicYear);

        return view('pdf/calendar_activities', $data);
    }
}
