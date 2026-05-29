<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\OrganizationModel;

class Announcements extends BaseController
{
    protected $announcementModel;
    protected $orgModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->orgModel = new OrganizationModel();
    }

    public function index()
    {
        $announcements = $this->announcementModel
            ->select('announcements.*, users.username as sender_name')
            ->join('users', 'users.id = announcements.sender_id', 'left')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Announcements',
            'announcements' => $announcements,
            'campuses' => OrganizationModel::CAMPUSES,
            'organizations' => $this->orgModel->findAll(),
        ];

        return view('admin/announcements/index', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'message' => 'required',
            'target_type' => 'required|in_list[all,campus,organization]',
        ];

        $targetType = $this->request->getPost('target_type');
        if ($targetType === 'campus') {
            $rules['target_value'] = 'required';
        } elseif ($targetType === 'organization') {
            $rules['target_value'] = 'required|numeric';
        }

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => \Config\Services::validation()->getErrors(),
                    'csrf' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', \Config\Services::validation()->getErrors());
        }

        $this->announcementModel->insert([
            'title' => $this->request->getPost('title'),
            'message' => $this->request->getPost('message'),
            'sender_id' => session()->get('user_id'),
            'target_type' => $targetType,
            'target_value' => $this->request->getPost('target_value'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement created successfully',
                'csrf' => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/announcements')->with('success', 'Announcement created successfully');
    }

    public function update($id)
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'message' => 'required',
            'target_type' => 'required|in_list[all,campus,organization]',
        ];

        $targetType = $this->request->getPost('target_type');
        if ($targetType === 'campus') {
            $rules['target_value'] = 'required';
        } elseif ($targetType === 'organization') {
            $rules['target_value'] = 'required|numeric';
        }

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => \Config\Services::validation()->getErrors(),
                    'csrf' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', \Config\Services::validation()->getErrors());
        }

        $this->announcementModel->update($id, [
            'title' => $this->request->getPost('title'),
            'message' => $this->request->getPost('message'),
            'target_type' => $targetType,
            'target_value' => $this->request->getPost('target_value'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement updated successfully',
                'csrf' => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/announcements')->with('success', 'Announcement updated successfully');
    }

    public function delete($id)
    {
        $this->announcementModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement deleted',
                'csrf' => csrf_hash(),
            ]);
        }

        return redirect()->to('/admin/announcements')->with('success', 'Announcement deleted');
    }

    public function toggleActive($id)
    {
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found', 'csrf' => csrf_hash()]);
        }

        $this->announcementModel->update($id, [
            'is_active' => $announcement['is_active'] ? 0 : 1,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'is_active' => !$announcement['is_active'],
            'csrf' => csrf_hash(),
        ]);
    }

    public function getOrgs()
    {
        $campus = $this->request->getGet('campus');
        $query = $this->orgModel->where('status', 'active');
        if ($campus) {
            $query->where('campus', $campus);
        }

        return $this->response->setJSON([
            'organizations' => $query->findAll(),
            'csrf' => csrf_hash(),
        ]);
    }
}
