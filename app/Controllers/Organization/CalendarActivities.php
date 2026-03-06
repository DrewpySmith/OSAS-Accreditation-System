<?php

namespace App\Controllers\Organization;

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

    public function index()
    {
        $organizationId = session()->get('organization_id');
        $academicYear = $this->request->getGet('year') ?? date('Y') . '-' . (date('Y') + 1);
        
        $data['activities'] = $this->activityModel->getByOrganizationAndYear($organizationId, $academicYear);
        $data['organization'] = $this->organizationModel->find($organizationId);
        $data['academic_year'] = $academicYear;
        $data['signatory'] = $this->signatoryModel->getByOrganizationAndYear($organizationId, $academicYear);
        
        return view('organization/forms/calendar_activities', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'academic_year' => 'required',
            'activity_date' => 'required|valid_date',
            'activity_title' => 'required',
            'responsible_person' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
                'csrf' => csrf_hash()
            ]);
        }

        $organizationId = session()->get('organization_id');
        $academicYear = $this->request->getPost('academic_year');

        $this->signatoryModel->upsertByOrganizationAndYear($organizationId, $academicYear, [
            'head_name' => $this->request->getPost('head_name'),
            'adviser_name' => $this->request->getPost('adviser_name'),
        ]);

        $data = [
            'organization_id' => $organizationId,
            'academic_year' => $academicYear,
            'activity_date' => $this->request->getPost('activity_date'),
            'activity_title' => $this->request->getPost('activity_title'),
            'responsible_person' => $this->request->getPost('responsible_person'),
            'remarks' => $this->request->getPost('remarks'),
            'status' => $this->request->getPost('status') ?? 'planned'
        ];

        $id = $this->activityModel->insert($data);

        if ($id) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Activity added successfully',
                'id' => $id,
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add activity',
            'csrf' => csrf_hash()
        ]);
    }

    public function update($id)
    {
        $activity = $this->activityModel->find($id);
        
        if (!$activity || $activity['organization_id'] != session()->get('organization_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Activity not found',
                'csrf' => csrf_hash()
            ]);
        }

        $organizationId = session()->get('organization_id');
        $academicYear = $activity['academic_year'];
        $this->signatoryModel->upsertByOrganizationAndYear($organizationId, $academicYear, [
            'head_name' => $this->request->getPost('head_name'),
            'adviser_name' => $this->request->getPost('adviser_name'),
        ]);

        $data = [
            'activity_date' => $this->request->getPost('activity_date'),
            'activity_title' => $this->request->getPost('activity_title'),
            'responsible_person' => $this->request->getPost('responsible_person'),
            'remarks' => $this->request->getPost('remarks'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->activityModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Activity updated successfully',
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update activity',
            'csrf' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        $activity = $this->activityModel->find($id);
        
        if (!$activity || $activity['organization_id'] != session()->get('organization_id')) {
            return redirect()->back()->with('error', 'Activity not found');
        }

        if ($this->activityModel->delete($id)) {
            return redirect()->back()->with('success', 'Activity deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete activity');
    }

    public function download($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $activities = $this->activityModel->getByOrganizationAndYear($organizationId, $academicYear);
        $organization = $this->organizationModel->find($organizationId);
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
        
        return $dompdf->stream('calendar_activities_' . $academicYear . '.pdf', ['Attachment' => true]);
    }

    public function print($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $data['activities'] = $this->activityModel->getByOrganizationAndYear($organizationId, $academicYear);
        $data['organization'] = $this->organizationModel->find($organizationId);
        $data['academic_year'] = $academicYear;
        $data['signatory'] = $this->signatoryModel->getByOrganizationAndYear($organizationId, $academicYear);
        
        return view('pdf/calendar_activities', $data);
    }
}