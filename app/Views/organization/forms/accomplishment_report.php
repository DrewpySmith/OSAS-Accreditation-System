<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accomplishment Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Navigation Bar */
        .navbar {
            background: #27ae60;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .navbar-title {
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        .navbar-buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back { background: #6c757d; color: white; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #333; }
        
        /* File Manager Style */
        .file-manager {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .file-manager-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .file-list {
            padding: 10px;
        }
        .file-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
            cursor: pointer;
        }
        .file-item:hover {
            background: #f8f9fa;
        }
        .file-icon {
            font-size: 32px;
            margin-right: 15px;
            width: 40px;
            text-align: center;
        }
        .file-info {
            flex: 1;
        }
        .file-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .file-meta {
            font-size: 13px;
            color: #7f8c8d;
        }
        .file-actions {
            display: flex;
            gap: 10px;
        }
        .file-actions button, .file-actions a {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        .badge-draft { background: #fff3cd; color: #856404; }
        .badge-submitted { background: #cce5ff; color: #004085; }
        .badge-approved { background: #d4edda; color: #155724; }
        .badge-rejected { background: #f8d7da; color: #721c24; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation Bar -->
        <div class="navbar">
            <div class="navbar-title">📁 Accomplishment Reports</div>
            <div class="navbar-buttons">
                <a href="<?= base_url('organization/dashboard') ?>" class="btn btn-back">← Back</a>
                <a href="<?= base_url('organization/accomplishment-report/create') ?>" class="btn btn-primary">+ New Report</a>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                ✓ <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- File Manager -->
        <div class="file-manager">
            <div class="file-manager-header">
                <div>
                    <span>📂 My Reports</span>
                    <span style="color: #7f8c8d; font-weight: normal; margin-left: 10px;">
                        (<?= count($reports) ?> items)
                    </span>
                </div>
                <div style="font-weight: normal; font-size: 13px; color: #7f8c8d;">
                    Sorted by: Date Created (Newest First)
                </div>
            </div>
            
            <div class="file-list">
                <?php if (empty($reports)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📄</div>
                        <h3 style="color: #2c3e50; margin-bottom: 10px;">No Reports Yet</h3>
                        <p style="color: #7f8c8d; margin-bottom: 20px;">
                            Create your first accomplishment report to get started
                        </p>
                        <a href="<?= base_url('organization/accomplishment-report/create') ?>" class="btn btn-primary">
                            + Create New Report
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($reports as $report): ?>
                        <div class="file-item">
                            <div class="file-icon">📄</div>
                            <div class="file-info">
                                <div class="file-name">
                                    <?= esc($report['activity_title']) ?>
                                    <span class="badge badge-<?= $report['status'] ?>">
                                        <?= ucfirst($report['status']) ?>
                                    </span>
                                </div>
                                <div class="file-meta">
                                    📅 <?= date('M d, Y', strtotime($report['created_at'])) ?> 
                                    • 📚 A.Y. <?= esc($report['academic_year']) ?>
                                    • ✏️ Last modified: <?= date('M d, Y h:i A', strtotime($report['updated_at'])) ?>
                                </div>
                            </div>
                            <div class="file-actions">
                                <a href="<?= base_url('organization/accomplishment-report/edit/' . $report['id']) ?>" 
                                   class="btn btn-warning">
                                    ✏️ Edit
                                </a>
                                <a href="<?= base_url('organization/accomplishment-report/download/' . $report['id']) ?>" 
                                   class="btn btn-success">
                                    📥 Download
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Box -->
        <div style="background: white; padding: 20px; border-radius: 10px; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #2c3e50;">📋 Report Status Guide</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <span class="badge badge-draft">Draft</span>
                    <p style="font-size: 13px; color: #7f8c8d; margin: 5px 0 0 0;">
                        Work in progress, not yet submitted
                    </p>
                </div>
                <div>
                    <span class="badge badge-submitted">Submitted</span>
                    <p style="font-size: 13px; color: #7f8c8d; margin: 5px 0 0 0;">
                        Waiting for admin review
                    </p>
                </div>
                <div>
                    <span class="badge badge-approved">Approved</span>
                    <p style="font-size: 13px; color: #7f8c8d; margin: 5px 0 0 0;">
                        Report has been approved
                    </p>
                </div>
                <div>
                    <span class="badge badge-rejected">Rejected</span>
                    <p style="font-size: 13px; color: #7f8c8d; margin: 5px 0 0 0;">
                        Needs revision - check comments
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>