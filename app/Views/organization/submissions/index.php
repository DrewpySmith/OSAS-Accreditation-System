<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Submissions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-reviewed {
            background: #cce5ff;
            color: #004085;
        }

        .badge-approved {
            background: #d4edda;
            color: #155724;
        }

        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>My Document Submissions</h2>
                <div>
                    <a href="<?= base_url('organization/dashboard') ?>" class="btn btn-info">← Back</a>
                    <a href="<?= base_url('organization/submissions/upload') ?>" class="btn btn-primary">📤 Upload
                        Document</a>
                </div>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Document Type</th>
                    <th>Title</th>
                    <th>Academic Year</th>
                    <th>File Name</th>
                    <th>Submitted Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 50px;">
                            No documents submitted yet. Click "Upload Document" to get started.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?= esc($document_types[$doc['document_type']] ?? str_replace('_', ' ', ucwords($doc['document_type']))) ?>
                            </td>
                            <td><strong><?= esc($doc['document_title']) ?></strong></td>
                            <td><?= esc($doc['academic_year']) ?></td>
                            <td><?= esc($doc['file_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($doc['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $doc['status'] ?>">
                                    <?= ucfirst($doc['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('organization/submissions/view/' . $doc['id']) ?>"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="<?= base_url('organization/submissions/download/' . $doc['id']) ?>"
                                    class="btn btn-success btn-sm">Download</a>
                                <?php if ($doc['status'] == 'pending'): ?>
                                    <a href="<?= base_url('organization/submissions/delete/' . $doc['id']) ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this document?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div
            style="background: white; padding: 20px; border-radius: 10px; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3>Status Legend:</h3>
            <p><span class="badge badge-pending">Pending</span> - Waiting for admin review</p>
            <p><span class="badge badge-reviewed">Reviewed</span> - Admin has reviewed your document</p>
            <p><span class="badge badge-approved">Approved</span> - Document approved</p>
            <p><span class="badge badge-rejected">Rejected</span> - Document needs revision (check comments)</p>
        </div>
    </div>
</body>

</html>