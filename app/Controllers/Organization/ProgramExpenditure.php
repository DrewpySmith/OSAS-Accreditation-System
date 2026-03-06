<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\ProgramExpenditureModel;
use App\Models\OrganizationModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ProgramExpenditure extends BaseController
{
    protected $expenditureModel;
    protected $organizationModel;

    public function __construct()
    {
        $this->expenditureModel = new ProgramExpenditureModel();
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        $organizationId = session()->get('organization_id');
        $academicYear = $this->request->getGet('year') ?? date('Y') . '-' . (date('Y') + 1);
        
        $data['expenditures'] = $this->expenditureModel->getByOrganizationAndYear($organizationId, $academicYear);
        $data['organization'] = $this->organizationModel->find($organizationId);
        $data['academic_year'] = $academicYear;
        $data['grand_total'] = $this->expenditureModel->getTotalByYear($organizationId, $academicYear);
        
        return view('organization/forms/program_expenditure', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'academic_year' => 'required',
            'fee_type' => 'required',
            'amount' => 'required|decimal',
            'frequency' => 'required',
            'number_of_students' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
                'csrf' => csrf_hash()
            ]);
        }

        $amount = floatval($this->request->getPost('amount'));
        $numStudents = intval($this->request->getPost('number_of_students'));

        $data = [
            'organization_id' => session()->get('organization_id'),
            'academic_year' => $this->request->getPost('academic_year'),
            'fee_type' => $this->request->getPost('fee_type'),
            'amount' => $amount,
            'frequency' => $this->request->getPost('frequency'),
            'number_of_students' => $numStudents,
            'total' => $amount * $numStudents
        ];

        $id = $this->expenditureModel->insert($data);

        if ($id) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Expenditure entry added successfully',
                'id' => $id,
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add expenditure entry',
            'csrf' => csrf_hash()
        ]);
    }

    public function update($id)
    {
        $expenditure = $this->expenditureModel->find($id);
        
        if (!$expenditure || $expenditure['organization_id'] != session()->get('organization_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Expenditure entry not found',
                'csrf' => csrf_hash()
            ]);
        }

        $amount = floatval($this->request->getPost('amount'));
        $numStudents = intval($this->request->getPost('number_of_students'));

        $data = [
            'fee_type' => $this->request->getPost('fee_type'),
            'amount' => $amount,
            'frequency' => $this->request->getPost('frequency'),
            'number_of_students' => $numStudents,
            'total' => $amount * $numStudents
        ];

        if ($this->expenditureModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Expenditure entry updated successfully',
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update expenditure entry',
            'csrf' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        $expenditure = $this->expenditureModel->find($id);
        
        if (!$expenditure || $expenditure['organization_id'] != session()->get('organization_id')) {
            return redirect()->back()->with('error', 'Expenditure entry not found');
        }

        if ($this->expenditureModel->delete($id)) {
            return redirect()->back()->with('success', 'Expenditure entry deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete expenditure entry');
    }

    public function download($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $expenditures = $this->expenditureModel->getByOrganizationAndYear($organizationId, $academicYear);
        $organization = $this->organizationModel->find($organizationId);
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
        
        return $dompdf->stream('program_expenditure_' . $academicYear . '.pdf', ['Attachment' => true]);
    }

    public function print($academicYear)
    {
        $organizationId = session()->get('organization_id');
        $data['expenditures'] = $this->expenditureModel->getByOrganizationAndYear($organizationId, $academicYear);
        $data['organization'] = $this->organizationModel->find($organizationId);
        $data['academic_year'] = $academicYear;
        $data['grand_total'] = $this->expenditureModel->getTotalByYear($organizationId, $academicYear);

        return view('pdf/program_expenditure', $data);
    }
}