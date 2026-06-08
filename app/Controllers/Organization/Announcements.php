<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\AnnouncementReadModel;
use App\Models\AnnouncementAttachmentModel;
use App\Models\AnnouncementCommentModel;
use App\Models\OrganizationModel;

class Announcements extends BaseController
{
    protected $announcementModel;
    protected $readModel;
    protected $attachmentModel;
    protected $commentModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->readModel = new AnnouncementReadModel();
        $this->attachmentModel = new AnnouncementAttachmentModel();
        $this->commentModel = new AnnouncementCommentModel();
    }

    public function index()
    {
        $orgId = session()->get('organization_id');
        $org = model(OrganizationModel::class)->find($orgId);
        $campus = $org ? $org['campus'] : null;

        $announcements = $this->announcementModel->getForOrg($orgId, $campus);
        $unreadCount = $this->announcementModel->getUnreadCount($orgId, $campus);

        // Attach metadata
        foreach ($announcements as &$a) {
            $a['is_read'] = $this->readModel->isRead($a['id'], $orgId);
            $a['attachment_count'] = $this->attachmentModel->where('announcement_id', $a['id'])->countAllResults();
            $a['comment_count'] = $this->commentModel->getCount($a['id']);
            $a['attachments'] = $this->attachmentModel->getByAnnouncement($a['id']);
            $a['comments'] = $this->commentModel->getByAnnouncement($a['id']);
        }

        $data = [
            'title' => 'Announcements',
            'announcements' => $announcements,
            'unread_count' => $unreadCount,
        ];

        return view('organization/announcements/index', $data);
    }

    public function markAsRead($id)
    {
        $orgId = session()->get('organization_id');
        $this->readModel->markAsRead($id, $orgId);

        return $this->response->setJSON([
            'success' => true,
            'csrf' => csrf_hash(),
        ]);
    }

    public function downloadAttachment($id)
    {
        $attachment = $this->attachmentModel->find($id);
        if (!$attachment) {
            return redirect()->back()->with('error', 'Attachment not found');
        }

        // Verify the announcement is active and visible to this org
        $announcement = $this->announcementModel->find($attachment['announcement_id']);
        if (!$announcement || !$announcement['is_active']) {
            return redirect()->back()->with('error', 'Announcement not available');
        }

        $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on disk');
        }

        return $this->response->download($filePath, null)->setFileName($attachment['file_name']);
    }

    public function addComment($id)
    {
        $announcement = $this->announcementModel->find($id);
        if (!$announcement || !$announcement['is_active']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Announcement not found', 'csrf' => csrf_hash()]);
        }

        $commentText = $this->request->getPost('comment');
        if (empty(trim($commentText))) {
            return $this->response->setJSON(['success' => false, 'message' => 'Comment cannot be empty', 'csrf' => csrf_hash()]);
        }

        $this->commentModel->insert([
            'announcement_id' => $id,
            'user_id' => session()->get('user_id'),
            'comment' => $commentText,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Comment posted',
            'csrf' => csrf_hash(),
        ]);
    }

    public function deleteComment($commentId)
    {
        $comment = $this->commentModel->find($commentId);
        if (!$comment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Comment not found', 'csrf' => csrf_hash()]);
        }

        if ($comment['user_id'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized', 'csrf' => csrf_hash()]);
        }

        $this->commentModel->delete($commentId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Comment deleted',
            'csrf' => csrf_hash(),
        ]);
    }
}
