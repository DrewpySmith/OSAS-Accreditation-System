<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrganizationModel;
use App\Models\UserModel;
use App\Models\OrganizationChecklistModel;
use App\Models\OrganizationRegistrationModel;
use App\Libraries\EmailService;
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

        $query = $this->organizationModel
            ->select('organizations.*, users.username as officer_email')
            ->join('users', 'users.organization_id = organizations.id AND users.role = "organization"', 'left');
            
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
            'username' => 'required|valid_email|is_unique[users.username]',
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
            $plainPassword = $this->request->getPost('password') ?: $this->request->getPost('new_password');
            $email = $this->request->getPost('username');
            $orgName = $this->request->getPost('name');

            $userData = [
                'username' => $email,
                'password' => $plainPassword,
                'role' => 'organization',
                'organization_id' => $orgId,
                'is_active' => 1
            ];

            $this->userModel->insert($userData);

            // Send welcome email with credentials
            EmailService::sendWelcomeCredentials($email, $orgName, $email, $plainPassword);

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

        $user = $this->userModel->where('organization_id', $id)->where('role', 'organization')->first();
        $userId = $user ? $user['id'] : null;

        $rules = [
            'name' => 'required|min_length[3]',
            'acronym' => 'permit_empty|max_length[50]',
            'campus' => 'required',
            'description' => 'permit_empty',
            'status' => 'required|in_list[active,inactive,suspended]',
            'username' => 'required|valid_email|is_unique[users.username,id,' . ($userId ?? '0') . ']'
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
            // Update username (email) and/or password
            if ($user) {
                $userData = [
                    'username' => $this->request->getPost('username')
                ];
                $newPassword = $this->request->getPost('new_password');
                if (!empty($newPassword)) {
                    $userData['password'] = $newPassword;
                }
                $this->userModel->update($user['id'], $userData);
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

        $allowedFields = [
            'application_letter', 'officer_list', 'commitment_forms',
            'constitution_bylaws', 'org_structure', 'calendar_activities',
            'financial_report', 'program_expenditures', 'accomplishment_report',
            'other', 'remarks'
        ];

        if (!in_array($field, $allowedFields)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid field'], 400);
        }

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

    /**
     * View pending registration requests
     */
    public function pendingList()
    {
        $regModel = new OrganizationRegistrationModel();
        
        // Fetch all non-approved signups
        $data['registrations'] = $regModel->orderBy('created_at', 'DESC')->findAll();
        $data['title'] = 'Pending Registrations';
        
        return view('admin/organizations/pending', $data);
    }

    /**
     * Approve organization registration and provision accounts
     */
    public function approveRegistration($id)
    {
        $regModel = new OrganizationRegistrationModel();
        $registration = $regModel->find($id);

        if (!$registration || $registration['status'] !== 'pending_admin') {
            return redirect()->back()->with('error', 'Registration request not found or not signed by adviser.');
        }

        // Generate dynamic academic year fallback
        $db = \Config\Database::connect();
        $ayRow = $db->table('academic_years')->where('is_current', 1)->get()->getRowArray();
        $academicYear = $ayRow ? $ayRow['year'] : '2024-2025';

        // 1. Create Organization in organizations table
        $orgData = [
            'name'        => $registration['name'],
            'acronym'     => $registration['acronym'],
            'campus'      => $registration['campus'],
            'description' => $registration['description'],
            'status'      => 'active'
        ];
        
        $orgId = $this->organizationModel->insert($orgData);

        if ($orgId) {
            // 2. Create User account in users table
            $userData = [
                'username'        => $registration['officer_email'],
                'password'        => $registration['officer_password'], // Already hashed on submitRegister
                'role'            => 'organization',
                'organization_id' => $orgId,
                'is_active'       => 1
            ];
            
            $this->userModel->insert($userData);

            // 3. Initialize organization checklist progress row automatically
            $checklistModel = new OrganizationChecklistModel();
            $checklistModel->insert([
                'organization_id' => $orgId,
                'academic_year'   => $academicYear,
                'application_letter' => 0,
                'officer_list' => 0,
                'commitment_forms' => 0,
                'constitution_bylaws' => 0,
                'org_structure' => 0,
                'calendar_activities' => 0,
                'financial_report' => 0,
                'program_expenditures' => 0,
                'accomplishment_report' => 0,
            ]);

            // 4. Update registration request status to approved
            $regModel->update($id, ['status' => 'approved']);

            // 5. Send Welcome & Credentials Email (plain pass placeholder since original is hashed, notify them of their set email)
            EmailService::sendWelcomeCredentials(
                $registration['officer_email'],
                $registration['name'],
                $registration['officer_email'],
                '[Password chosen during registration]'
            );

            return redirect()->to('/admin/organizations/pending')->with('success', 'Organization ' . $registration['name'] . ' approved successfully! Account credentials have been emailed.');
        }

        return redirect()->back()->with('error', 'Failed to approve the registration request.');
    }

    /**
     * Reject organization registration request
     */
    public function rejectRegistration($id)
    {
        $regModel = new OrganizationRegistrationModel();
        $registration = $regModel->find($id);

        if (!$registration) {
            return redirect()->back()->with('error', 'Registration request not found.');
        }

        if ($regModel->update($id, ['status' => 'rejected'])) {
            return redirect()->to('/admin/organizations/pending')->with('success', 'Organization registration request has been rejected.');
        }

        return redirect()->back()->with('error', 'Failed to reject the registration request.');
    }
}