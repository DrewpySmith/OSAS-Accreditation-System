<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\OrganizationRegistrationModel;
use App\Models\OrganizationModel;
use App\Libraries\EmailService;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to($this->getRedirectPath());
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters',
                    'max_length' => 'Username cannot exceed 100 characters'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must be at least 8 characters'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid username or password');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid username or password');
        }

        if (!$user['is_active']) {
            return redirect()->back()->with('error', 'Your account is inactive. Please contact admin.');
        }

        // Regenerate session ID to prevent session fixation
        session()->regenerate();

        // Update last login
        $userModel->updateLastLogin($user['id']);

        // Set session
        $sessionData = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'organization_id' => $user['organization_id'],
            'logged_in' => true
        ];

        session()->set($sessionData);

        return redirect()->to($this->getRedirectPath());
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully');
    }

    public function changePassword()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('auth/change_password');
    }

    public function updatePassword()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'current_password' => [
                'label' => 'Current Password',
                'rules' => 'required',
                'errors' => ['required' => 'Current password is required']
            ],
            'new_password' => [
                'label' => 'New Password',
                'rules' => 'required|min_length[8]|max_length[255]',
                'errors' => [
                    'required' => 'New password is required',
                    'min_length' => 'New password must be at least 8 characters',
                    'max_length' => 'New password cannot exceed 255 characters'
                ]
            ],
            'confirm_password' => [
                'label' => 'Confirm Password',
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Please confirm your new password',
                    'matches' => 'Passwords do not match'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }

        $userModel->update($user['id'], ['password' => $newPassword]);

        return redirect()->back()->with('success', 'Password changed successfully');
    }

    private function getRedirectPath()
    {
        $role = session()->get('role');
        
        if ($role === 'admin') {
            return '/admin/dashboard';
        } else {
            return '/organization/dashboard';
        }
    }

    /**
     * View registration portal
     */
    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to($this->getRedirectPath());
        }

        return view('auth/register', [
            'campuses' => OrganizationModel::CAMPUSES
        ]);
    }

    /**
     * Submit new organization signup request
     */
    public function submitRegister()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'name'             => 'required|min_length[3]|max_length[255]',
            'acronym'          => 'permit_empty|min_length[2]|max_length[50]',
            'campus'           => 'required',
            'description'      => 'permit_empty',
            'officer_email'    => 'required|valid_email|is_unique[users.username]|is_unique[organization_registrations.officer_email]',
            'officer_password' => 'required|min_length[8]',
            'adviser_name'     => 'required|min_length[3]|max_length[255]',
            'adviser_email'    => 'required|valid_email',
        ], [
            'officer_email' => [
                'is_unique' => 'This email address is already in use by an active user or pending signup.'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $regModel = new OrganizationRegistrationModel();
        
        $data = [
            'name'             => $this->request->getPost('name'),
            'acronym'          => $this->request->getPost('acronym'),
            'campus'           => $this->request->getPost('campus'),
            'description'      => $this->request->getPost('description'),
            'officer_email'    => $this->request->getPost('officer_email'),
            'officer_password' => password_hash($this->request->getPost('officer_password'), PASSWORD_BCRYPT),
            'adviser_name'     => $this->request->getPost('adviser_name'),
            'adviser_email'    => $this->request->getPost('adviser_email'),
            'adviser_token'    => $regModel->generateToken(),
            'status'           => 'pending_adviser',
        ];

        if ($regModel->insert($data)) {
            // Trigger Adviser Notification Email
            EmailService::sendAdviserVerification($data['adviser_email'], $data);
            return redirect()->to('/login')->with('success', 'Signup request submitted! An email has been sent to your adviser to sign and validate your application.');
        }

        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    /**
     * Adviser validation page (Commitment Form & Signature Canvas)
     */
    public function validateAdviser($token)
    {
        $regModel = new OrganizationRegistrationModel();
        $registration = $regModel->where('adviser_token', $token)->first();

        if (!$registration) {
            return redirect()->to('/login')->with('error', 'Invalid or expired validation link.');
        }

        if ($registration['status'] !== 'pending_adviser') {
            return redirect()->to('/login')->with('error', 'This registration request has already been validated.');
        }

        return view('auth/validate_adviser', [
            'registration' => $registration
        ]);
    }

    /**
     * Submit Adviser Signature base64 image
     */
    public function submitAdviserSignature($token)
    {
        $regModel = new OrganizationRegistrationModel();
        $registration = $regModel->where('adviser_token', $token)->first();

        if (!$registration || $registration['status'] !== 'pending_adviser') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid or expired validation link.'
            ]);
        }

        $signatureBase64 = $this->request->getPost('signature');
        if (empty($signatureBase64)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Signature is required.'
            ]);
        }

        // Validate base64 format
        if (!preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $signatureBase64)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid signature format.'
            ]);
        }

        // Save canvas signature image
        $signatureBase64 = preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $signatureBase64);
        $signatureBase64 = str_replace(' ', '+', $signatureBase64);
        $imageDecoded = base64_decode($signatureBase64, true);

        if ($imageDecoded === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid signature data.'
            ]);
        }

        // Validate decoded image size (max 5MB)
        if (strlen($imageDecoded) > 5 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Signature file too large.'
            ]);
        }

        // Verify it's a valid image
        $imageInfo = @getimagesizefromstring($imageDecoded);
        if ($imageInfo === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid signature image.'
            ]);
        }

        $fileName = 'sig_' . $registration['id'] . '_' . time() . '.png';
        $uploadDir = ROOTPATH . 'public/uploads/signatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        file_put_contents($uploadDir . $fileName, $imageDecoded);

        // Update registration record
        $updateData = [
            'signature_path' => 'uploads/signatures/' . $fileName,
            'status'         => 'pending_admin',
            'signed_at'      => date('Y-m-d H:i:s')
        ];

        if ($regModel->update($registration['id'], $updateData)) {
            // Notify Admin via Email
            $db = \Config\Database::connect();
            $adminSetting = $db->table('system_settings')->where('setting_key', 'system_email')->get()->getRowArray();
            $adminEmail = $adminSetting ? $adminSetting['setting_value'] : 'admin@usg-accreditation.com';
            
            EmailService::sendAdminNotice($adminEmail, array_merge($registration, $updateData));

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Thank you! Your signature has been verified. The application is now sent to the USG Administrator for final review.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to save your signature. Please try again.'
        ]);
    }

    /**
     * Send Password Recovery Link via SMTP
     */
    public function sendResetLink()
    {
        $email = $this->request->getPost('email');
        if (empty($email)) {
            return redirect()->back()->with('error', 'Email address is required');
        }

        $userModel = new UserModel();
        $user = $userModel->where('username', $email)->first(); // Remember: organization usernames are emails

        if (!$user) {
            // Decoy success to prevent email enumeration attacks
            return redirect()->to('/login')->with('success', 'If the email exists, a password reset link has been sent.');
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        $db = \Config\Database::connect();
        
        // Delete previous resets
        $db->table('password_resets')->where('email', $email)->delete();

        // Save new reset
        $db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Send Email
        EmailService::sendPasswordReset($email, $token);

        return redirect()->to('/login')->with('success', 'If the email exists, a password reset link has been sent.');
    }

    /**
     * Reset password form page
     */
    public function resetPassword()
    {
        $token = $this->request->getGet('token');
        if (empty($token)) {
            return redirect()->to('/login')->with('error', 'Missing password reset token.');
        }

        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')
                    ->where('token', $token)
                    ->where('expires_at >=', date('Y-m-d H:i:s'))
                    ->get()
                    ->getRowArray();

        if (!$reset) {
            return redirect()->to('/login')->with('error', 'Invalid or expired password reset link.');
        }

        return view('auth/reset_password', [
            'token' => $token
        ]);
    }

    /**
     * Save new password
     */
    public function updateForgotPassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $confirm = $this->request->getPost('confirm_password');

        if (empty($token) || empty($password) || empty($confirm)) {
            return redirect()->back()->with('error', 'All fields are required');
        }

        if ($password !== $confirm) {
            return redirect()->back()->with('error', 'Passwords do not match');
        }

        if (strlen($password) < 8) {
            return redirect()->back()->with('error', 'Password must be at least 8 characters');
        }

        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')
                    ->where('token', $token)
                    ->where('expires_at >=', date('Y-m-d H:i:s'))
                    ->get()
                    ->getRowArray();

        if (!$reset) {
            return redirect()->to('/login')->with('error', 'Invalid or expired password reset token.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('username', $reset['email'])->first();

        if ($user) {
            // Update password
            $userModel->update($user['id'], [
                'password' => password_hash($password, PASSWORD_BCRYPT)
            ]);
            
            // Delete token
            $db->table('password_resets')->where('email', $reset['email'])->delete();

            return redirect()->to('/login')->with('success', 'Your password has been successfully reset. You can now log in.');
        }

        return redirect()->to('/login')->with('error', 'User account not found.');
    }
}