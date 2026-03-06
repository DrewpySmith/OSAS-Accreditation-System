<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

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
                'rules' => 'required|min_length[3]|max_length[100]|alpha_numeric',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters',
                    'max_length' => 'Username cannot exceed 100 characters',
                    'alpha_numeric' => 'Username can only contain letters and numbers'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must be at least 6 characters'
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
                'rules' => 'required|min_length[6]|max_length[255]',
                'errors' => [
                    'required' => 'New password is required',
                    'min_length' => 'New password must be at least 6 characters',
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
}