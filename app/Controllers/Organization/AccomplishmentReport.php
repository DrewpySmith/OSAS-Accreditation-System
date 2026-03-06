<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\AccomplishmentReportModel;
use App\Models\OrganizationModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class AccomplishmentReport extends BaseController
{
    protected $reportModel;
    protected $organizationModel;

    public function __construct()
    {
        $this->reportModel = new AccomplishmentReportModel();
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        $organizationId = session()->get('organization_id');
        $data['reports'] = $this->reportModel->getByOrganization($organizationId);
        $data['organization'] = $this->organizationModel->find($organizationId);
        
        return view('organization/forms/accomplishment_report', $data);
    }

    public function create()
    {
        $organizationId = session()->get('organization_id');
        $data['organization'] = $this->organizationModel->find($organizationId);
        
        return view('organization/forms/accomplishment_report_form', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'academic_year' => 'required',
            'activity_title' => 'required',
            'narrative_report' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $organizationId = session()->get('organization_id');

        $data = [
            'organization_id' => $organizationId,
            'academic_year' => $this->request->getPost('academic_year'),
            'activity_title' => $this->request->getPost('activity_title'),
            'narrative_report' => $this->request->getPost('narrative_report'),
            'pictorials' => json_encode([]),
            'activity_designs' => json_encode([]),
            'evaluation_sheets' => json_encode([]),
            'status' => $this->request->getPost('status') ?? 'draft'
        ];

        $reportId = $this->reportModel->insert($data);

        if ($reportId) {
            // Handle file uploads
            $this->handleFileUploads($reportId, $organizationId);
            
            return redirect()->to('/organization/accomplishment-report')
                ->with('success', 'Accomplishment report created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create accomplishment report');
    }

    public function edit($id)
    {
        $report = $this->reportModel->getWithFiles($id);
        
        if (!$report || $report['organization_id'] != session()->get('organization_id')) {
            return redirect()->to('/organization/accomplishment-report')
                ->with('error', 'Report not found');
        }

        $data['report'] = $report;
        $data['organization'] = $this->organizationModel->find(session()->get('organization_id'));
        
        return view('organization/forms/accomplishment_report_form', $data);
    }

    public function update($id)
    {
        $report = $this->reportModel->find($id);
        
        if (!$report || $report['organization_id'] != session()->get('organization_id')) {
            return redirect()->to('/organization/accomplishment-report')
                ->with('error', 'Report not found');
        }

        $data = [
            'academic_year' => $this->request->getPost('academic_year'),
            'activity_title' => $this->request->getPost('activity_title'),
            'narrative_report' => $this->request->getPost('narrative_report'),
            'status' => $this->request->getPost('status') ?? 'draft'
        ];

        if ($this->reportModel->update($id, $data)) {
            // Handle file uploads
            $this->handleFileUploads($id, session()->get('organization_id'));
            
            return redirect()->to('/organization/accomplishment-report')
                ->with('success', 'Accomplishment report updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update accomplishment report');
    }

    private function handleFileUploads($reportId, $organizationId)
    {
        $uploadPath = WRITEPATH . 'uploads/accomplishment/' . $organizationId . '/';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Handle pictorials
        $pictorials = $this->request->getFileMultiple('pictorials');
        if ($pictorials && is_array($pictorials)) {
            foreach ($pictorials as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $subPath = $uploadPath . 'pictorials/';
                    if (!is_dir($subPath)) {
                        mkdir($subPath, 0777, true);
                    }
                    $file->move($subPath, $newName);
                    $this->reportModel->addFile($reportId, 'pictorials', 
                        'accomplishment/' . $organizationId . '/pictorials/' . $newName);
                }
            }
        }

        // Handle activity designs
        $designs = $this->request->getFileMultiple('activity_designs');
        if ($designs && is_array($designs)) {
            foreach ($designs as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $subPath = $uploadPath . 'designs/';
                    if (!is_dir($subPath)) {
                        mkdir($subPath, 0777, true);
                    }
                    $file->move($subPath, $newName);
                    $this->reportModel->addFile($reportId, 'activity_designs', 
                        'accomplishment/' . $organizationId . '/designs/' . $newName);
                }
            }
        }

        // Handle evaluation sheets
        $evaluations = $this->request->getFileMultiple('evaluation_sheets');
        if ($evaluations && is_array($evaluations)) {
            foreach ($evaluations as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $subPath = $uploadPath . 'evaluations/';
                    if (!is_dir($subPath)) {
                        mkdir($subPath, 0777, true);
                    }
                    $file->move($subPath, $newName);
                    $this->reportModel->addFile($reportId, 'evaluation_sheets', 
                        'accomplishment/' . $organizationId . '/evaluations/' . $newName);
                }
            }
        }
    }

    public function download($id)
    {
        $report = $this->reportModel->getWithFiles($id);
        
        if (!$report || $report['organization_id'] != session()->get('organization_id')) {
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

        if (!$report || $report['organization_id'] != session()->get('organization_id')) {
            return redirect()->back()->with('error', 'Report not found');
        }

        return view('pdf/accomplishment_report', ['report' => $report]);
    }
}