<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-info { background: #17a2b8; color: white; }
        .btn-success { background: #28a745; color: white; }
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-reviewed { background: #cce5ff; color: #004085; }
        .badge-approved { background: #d4edda; color: #155724; }
        .badge-rejected { background: #f8d7da; color: #721c24; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .info-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
        }
        .info-label {
            font-size: 12px;
            color: #7f8c8d;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .info-value { color: #2c3e50; }
        .event-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 3px solid #007bff;
        }
        .event-header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }
        .event-author { font-weight: 600; color: #2c3e50; }
        .event-date { color: #7f8c8d; font-size: 13px; }
        @media (max-width: 720px) {
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display:flex; justify-content: space-between; align-items: start; gap: 15px;">
                <div>
                    <a href="<?= base_url('organization/submissions') ?>" class="btn btn-info">← Back</a>
                    <h2 style="margin: 15px 0 5px 0; color:#2c3e50;"><?= esc($document['document_title'] ?? '') ?></h2>
                    <div style="color:#7f8c8d;">
                        <?= esc(str_replace('_', ' ', ucwords($document['document_type'] ?? ''))) ?>
                    </div>
                </div>
                <div>
                    <span class="badge badge-<?= esc($document['status'] ?? 'pending') ?>">
                        <?= ucfirst($document['status'] ?? 'pending') ?>
                    </span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Academic Year</div>
                    <div class="info-value"><?= esc($document['academic_year'] ?? '') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Submitted Date</div>
                    <div class="info-value"><?= !empty($document['created_at']) ? date('M d, Y h:i A', strtotime($document['created_at'])) : '' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">File Name</div>
                    <div class="info-value"><?= esc($document['file_name'] ?? '') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">File Size</div>
                    <div class="info-value"><?= !empty($document['file_size']) ? number_format(($document['file_size'] ?? 0) / 1024, 2) . ' KB' : '' ?></div>
                </div>
            </div>

            <?php if (!empty($document['description'])): ?>
                <div style="margin-top: 15px;">
                    <div class="info-label">Description</div>
                    <div class="info-value"><?= nl2br(esc($document['description'])) ?></div>
                </div>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <a href="<?= base_url('organization/submissions/download/' . ($document['id'] ?? '')) ?>" class="btn btn-success">Download</a>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Review History</h3>
            <?php if (!empty($review_history)): ?>
                <?php foreach ($review_history as $event): ?>
                    <div class="event-box">
                        <div class="event-header">
                            <div class="event-author">
                                <?= esc($event['username'] ?? 'System') ?>
                                <?php if (!empty($event['role'])): ?>
                                    <span style="color:#7f8c8d; font-weight: normal;">(<?= ucfirst($event['role']) ?>)</span>
                                <?php endif; ?>
                            </div>
                            <div class="event-date">
                                <?= !empty($event['created_at']) ? date('M d, Y h:i A', strtotime($event['created_at'])) : '' ?>
                            </div>
                        </div>

                        <?php if (($event['action'] ?? '') === 'status_change'): ?>
                            <div style="color:#2c3e50;">
                                Status changed
                                <?php if (!empty($event['from_status'])): ?>
                                    from <strong><?= esc($event['from_status']) ?></strong>
                                <?php endif; ?>
                                to <strong><?= esc($event['to_status'] ?? '') ?></strong>
                            </div>
                        <?php elseif (($event['action'] ?? '') === 'comment'): ?>
                            <div style="color:#2c3e50;"><strong>Comment:</strong></div>
                            <div style="color:#2c3e50; margin-top: 6px;">
                                <?= nl2br(esc($event['comment'] ?? '')) ?>
                            </div>
                        <?php else: ?>
                            <div style="color:#2c3e50;">
                                <?= esc($event['action'] ?? 'activity') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#7f8c8d;">No review activity yet.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Comments</h3>
            <?php if (!empty($document['comments'])): ?>
                <?php foreach ($document['comments'] as $comment): ?>
                    <div class="event-box">
                        <div class="event-header">
                            <div class="event-author">
                                <?= esc($comment['username'] ?? '') ?>
                                <span style="color:#7f8c8d; font-weight: normal;">(<?= ucfirst($comment['role'] ?? '') ?>)</span>
                            </div>
                            <div class="event-date">
                                <?= !empty($comment['created_at']) ? date('M d, Y h:i A', strtotime($comment['created_at'])) : '' ?>
                            </div>
                        </div>
                        <div style="color:#2c3e50;">
                            <?= nl2br(esc($comment['comment'] ?? '')) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#7f8c8d;">No comments yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
