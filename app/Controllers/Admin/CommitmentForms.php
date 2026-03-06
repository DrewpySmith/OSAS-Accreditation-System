<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommitmentFormModel;
use App\Models\OrganizationModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class CommitmentForms extends BaseController
{
    protected $commitmentModel;
    protected $organizationModel;

    public function __construct()
    {
        $this->commitmentModel = new CommitmentFormModel();
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        $filters = [];

        if ($this->request->getGet('organization_id')) {
            $filters['organization_id'] = $this->request->getGet('organization_id');
        }

        $builder = $this->commitmentModel->select('commitment_forms.*, organizations.name as org_name')
            ->join('organizations', 'organizations.id = commitment_forms.organization_id');

        if (isset($filters['organization_id']) && $filters['organization_id'] !== '') {
            $builder->where('commitment_forms.organization_id', $filters['organization_id']);
        }

        $data['forms'] = $builder->orderBy('commitment_forms.created_at', 'DESC')->findAll();
        $data['organizations'] = $this->organizationModel->findAll();

        return view('admin/commitment_forms/index', $data);
    }

    public function download($id)
    {
        $form = $this->commitmentModel->find($id);

        if (!$form) {
            return redirect()->back()->with('error', 'Form not found');
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('pdf/commitment_form', ['form' => $form]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('commitment_form_' . $id . '.pdf', ['Attachment' => true]);
    }

    public function print($id)
    {
        $form = $this->commitmentModel->find($id);

        if (!$form) {
            return redirect()->back()->with('error', 'Form not found');
        }

        $data['form'] = $form;
        return view('pdf/commitment_form', $data);
    }
}
