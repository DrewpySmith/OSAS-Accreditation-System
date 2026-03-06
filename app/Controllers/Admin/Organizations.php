<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrganizationModel;
use App\Models\UserModel;
use App\Models\OrganizationChecklistModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Organizations extends BaseController
{
    protected $organizationModel;
    protected $userModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $campus = $this->request->getGet('campus');

        $query = $this->organizationModel;
        if (!empty($campus)) {
            $query = $query->where('campus', $campus);
        }

        $data['organizations'] = $query->findAll();
        $data['campuses'] = OrganizationModel::CAMPUSES;
        $data['selected_campus'] = $campus;

        return view('admin/organizations/index', $data);
    }

    public function create()
    {
        $data['campuses'] = OrganizationModel::CAMPUSES;
        return view('admin/organizations/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|min_length[3]',
            'acronym' => 'permit_empty|max_length[50]',
            'campus' => 'required',
            'description' => 'permit_empty',
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'password' => 'permit_empty', // Combined with new_password below
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors(),
                    'csrf' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Create organization
        $orgData = [
            'name' => $this->request->getPost('name'),
            'acronym' => $this->request->getPost('acronym'),
            'campus' => $this->request->getPost('campus'),
            'description' => $this->request->getPost('description'),
            'status' => 'active'
        ];

        $orgId = $this->organizationModel->insert($orgData);

        if ($orgId) {
            // Create user account
            $userData = [
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password') ?: $this->request->getPost('new_password'),
                'role' => 'organization',
                'organization_id' => $orgId,
                'is_active' => 1
            ];

            $this->userModel->insert($userData);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Organization created successfully',
                    'csrf' => csrf_hash()
                ]);
            }

            return redirect()->to('/admin/organizations')->with('success', 'Organization created successfully');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create organization',
                'csrf' => csrf_hash()
            ]);
        }
        return redirect()->back()->with('error', 'Failed to create organization');
    }

    public function edit($id)
    {
        $data['organization'] = $this->organizationModel->find($id);

        if (!$data['organization']) {
            return redirect()->to('/admin/organizations')->with('error', 'Organization not found');
        }

        $data['user'] = $this->userModel->where('organization_id', $id)->first();
        $data['campuses'] = OrganizationModel::CAMPUSES;

        return view('admin/organizations/edit', $data);
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|min_length[3]',
            'acronym' => 'permit_empty|max_length[50]',
            'campus' => 'required',
            'description' => 'permit_empty',
            'status' => 'required|in_list[active,inactive,suspended]'
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors(),
                    'csrf' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $orgData = [
            'name' => $this->request->getPost('name'),
            'acronym' => $this->request->getPost('acronym'),
            'campus' => $this->request->getPost('campus'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->organizationModel->update($id, $orgData)) {
            // Update user if password is provided
            $newPassword = $this->request->getPost('new_password');
            if (!empty($newPassword)) {
                $user = $this->userModel->where('organization_id', $id)->first();
                if ($user) {
                    $this->userModel->update($user['id'], ['password' => $newPassword]);
                }
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Organization updated successfully',
                    'csrf' => csrf_hash()
                ]);
            }

            return redirect()->to('/admin/organizations')->with('success', 'Organization updated successfully');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update organization',
                'csrf' => csrf_hash()
            ]);
        }
        return redirect()->back()->with('error', 'Failed to update organization');
    }

    public function delete($id)
    {
        if ($this->organizationModel->delete($id)) {
            return redirect()->to('/admin/organizations')->with('success', 'Organization deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete organization');
    }

    public function view($id)
    {
        $data['organization'] = $this->organizationModel->getOrganizationWithStats($id);

        if (!$data['organization']) {
            return redirect()->to('/admin/organizations')->with('error', 'Organization not found');
        }

        // Fetch current academic year
        $db = \Config\Database::connect();
        $currentAY = $db->table('academic_years')->where('is_current', 1)->get()->getRowArray();
        $academicYear = $currentAY ? $currentAY['year'] : '2024-2025';

        $checklistModel = new \App\Models\OrganizationChecklistModel();
        $checklist = $checklistModel->getByOrgAndYear($id, $academicYear);

        if (!$checklist) {
            // Create default checklist if it doesn't exist
            $checklistModel->insert([
                'organization_id' => $id,
                'academic_year' => $academicYear
            ]);
            $checklist = $checklistModel->getByOrgAndYear($id, $academicYear);
        }

        $data['checklist'] = $checklist;
        $data['academic_year'] = $academicYear;
        $data['document_types'] = \App\Models\DocumentSubmissionModel::DOCUMENT_TYPES;

        return view('admin/organizations/view', $data);
    }

    public function details($id)
    {
        $organization = $this->organizationModel->getOrganizationWithStats($id);

        if (!$organization) {
            return $this->response->setJSON(['error' => 'Organization not found'], 404);
        }

        // Fetch current academic year
        $db = \Config\Database::connect();
        $currentAY = $db->table('academic_years')->where('is_current', 1)->get()->getRowArray();
        $academicYear = $currentAY ? $currentAY['year'] : '2024-2025';

        $checklistModel = new \App\Models\OrganizationChecklistModel();
        $checklistData = $checklistModel->getByOrgAndYear($id, $academicYear);

        $checklist = [];
        $labels = [
            'application_letter' => 'Application Letter',
            'officer_list' => 'Officer List',
            'commitment_forms' => 'Commitment Forms',
            'constitution_bylaws' => 'Constitution & By-Laws',
            'org_structure' => 'Org Structure',
            'calendar_activities' => 'Calendar of Activities',
            'financial_report' => 'Financial Report',
            'program_expenditures' => 'Program Expenditures',
            'accomplishment_report' => 'Accomplishment Report'
        ];

        if ($checklistData) {
            foreach ($labels as $field => $label) {
                $checklist[] = [
                    'label' => $label,
                    'is_checked' => isset($checklistData[$field]) ? (bool) $checklistData[$field] : false,
                    'updated_at' => $checklistData['updated_at'] ?? null
                ];
            }
        }

        // Fetch user for username/email info
        $user = $db->table('users')->where('organization_id', $id)->where('role', 'organization')->get()->getRowArray();

        return $this->response->setJSON([
            'id' => $organization['id'],
            'name' => $organization['name'],
            'acronym' => $organization['acronym'],
            'email' => $user ? $user['username'] : 'N/A', // Using username as email/contact identifier
            'description' => $organization['description'],
            'academic_year' => $academicYear,
            'is_accredited' => $organization['is_accredited'],
            'stats' => [
                'total_submissions' => $organization['total_submissions'] ?? 0,
                'approved_docs' => $organization['approved_docs'] ?? 0,
                'pending_docs' => $organization['pending_docs'] ?? 0,
                'completion_percentage' => $organization['completion_percentage'] ?? 0
            ],
            'checklist' => $checklist
        ]);
    }

    public function updateChecklist($id)
    {
        $field = $this->request->getPost('field');
        $value = $this->request->getPost('value');
        $academicYear = $this->request->getPost('academic_year');

        $checklistModel = new \App\Models\OrganizationChecklistModel();

        $checklist = $checklistModel->getByOrgAndYear($id, $academicYear);

        if ($checklist) {
            $checklistModel->update($checklist['id'], [
                $field => $value
            ]);
            return $this->response->setJSON([
                'status' => 'success',
                'csrf' => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Checklist not found'], 404);
    }

    public function certificate($organizationId, $academicYear)
    {
        $checklistModel = new OrganizationChecklistModel();
        $checklist = $checklistModel->getByOrgAndYear($organizationId, $academicYear);

        if (!$checklistModel->isComplete($checklist)) {
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

    public function printCertificate($organizationId, $academicYear)
    {
        $checklistModel = new OrganizationChecklistModel();
        $checklist = $checklistModel->getByOrgAndYear($organizationId, $academicYear);

        if (!$checklistModel->isComplete($checklist)) {
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

    public function printOrgList()
    {
        $campus = $this->request->getGet('campus');

        $query = $this->organizationModel;
        if (!empty($campus)) {
            $query = $query->where('campus', $campus);
        }

        $data['organizations'] = $query->findAll();
        $data['selected_campus'] = $campus;
        $data['title'] = 'List of Organizations' . (!empty($campus) ? ' - ' . $campus . ' Campus' : '');

        return view('admin/organizations/print', $data);
    }
}