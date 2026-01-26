<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Organization - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-logout {
            padding: 8px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .back-link {
            color: #3498db;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 20px;
        }

        .header h2 {
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .muted {
            color: #7f8c8d;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-suspended {
            background: #fff3cd;
            color: #856404;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .info-value {
            color: #2c3e50;
            font-size: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #3498db;
        }

        .stat-card h3 {
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        /* Checklist Styles */
        .checklist-card {
            margin-top: 30px;
        }

        .checklist-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            border-bottom: 1px solid #edf2f7;
            gap: 15px;
            transition: background 0.2s;
        }

        .checklist-item:hover {
            background: #f8fafc;
        }

        .checklist-item:last-child {
            border-bottom: none;
        }

        .checklist-item input[type="checkbox"] {
            margin-top: 4px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checklist-item label {
            flex: 1;
            cursor: pointer;
            color: #2d3748;
            font-size: 15px;
            line-height: 1.5;
        }

        .checklist-item.completed label {
            text-decoration: line-through;
            color: #a0aec0;
        }

        .checklist-item small {
            display: block;
            margin-top: 4px;
            color: #718096;
            font-size: 13px;
            line-height: 1.4;
        }

        /* Certificate Section */
        .certificate-section {
            margin-top: 30px;
            padding: 25px;
            background: #fff;
            border: 2px dashed #d4af37;
            border-radius: 10px;
            text-align: center;
        }

        .cert-btn-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>USG Accreditation - Admin Panel</h1>
        <div class="navbar-right">
            <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <a href="<?= base_url('admin/organizations') ?>">Organizations</a>
            <a href="<?= base_url('admin/documents') ?>">Documents</a>
            <a href="<?= base_url('admin/statistics') ?>">Statistics</a>
            <a href="<?= base_url('logout') ?>" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <a href="<?= base_url('admin/organizations') ?>" class="back-link">← Back to Organizations</a>

        <div class="card">
            <div class="header">
                <div>
                    <h2><?= esc($organization['name'] ?? '') ?></h2>
                    <?php if (!empty($organization['acronym'])): ?>
                        <div class="muted"><?= esc($organization['acronym']) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <?php $status = $organization['status'] ?? 'active'; ?>
                    <span class="badge badge-<?= esc($status) ?>"><?= ucfirst(esc($status)) ?></span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Description</div>
                    <div class="info-value"><?= esc($organization['description'] ?? '') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Created At</div>
                    <div class="info-value">
                        <?= !empty($organization['created_at']) ? date('M d, Y', strtotime($organization['created_at'])) : '' ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Updated At</div>
                    <div class="info-value">
                        <?= !empty($organization['updated_at']) ? date('M d, Y', strtotime($organization['updated_at'])) : '' ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Organization ID</div>
                    <div class="info-value"><?= esc($organization['id'] ?? '') ?></div>
                </div>
            </div>

            <div class="actions">
                <?php if (!empty($organization['id'])): ?>
                    <a href="<?= base_url('admin/organizations/edit/' . $organization['id']) ?>"
                        class="btn btn-warning">Edit</a>
                    <a href="<?= base_url('admin/statistics/organization/' . $organization['id']) ?>"
                        class="btn btn-primary">View Statistics</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Documents</h3>
                <div class="number"><?= esc($organization['total_documents'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Activities</h3>
                <div class="number"><?= esc($organization['total_activities'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Financial Reports</h3>
                <div class="number"><?= esc($organization['total_financial_reports'] ?? 0) ?></div>
            </div>
        </div>
        <div class="card checklist-card">
            <div class="header" style="margin-bottom: 20px;">
                <h3>Accreditation Checklist (<?= esc($academic_year) ?>)</h3>
            </div>

            <div class="checklist-items">
                <?php
                $items = array_filter($document_types ?? [], function ($key) {
                    return $key !== 'other';
                }, ARRAY_FILTER_USE_KEY);

                foreach ($items as $field => $label):
                    $isCompleted = isset($checklist[$field]) && $checklist[$field] == 1;
                    ?>
                    <div class="checklist-item <?= $isCompleted ? 'completed' : '' ?>" id="item_<?= $field ?>">
                        <input type="checkbox" id="chk_<?= $field ?>" <?= $isCompleted ? 'checked' : '' ?>
                            onchange="updateChecklist('<?= $field ?>', this.checked)">
                        <label for="chk_<?= $field ?>">
                            <?= $label ?>
                            <?php if ($field === 'financial_report'): ?>
                                <small>Signed by the outgoing treasurer/auditor noted by the Faculty Adviser and Approved by the
                                    Dean/Campus Director including supporting documents such as official receipts.</small>
                            <?php elseif ($field === 'accomplishment_report'): ?>
                                <small>Includes Results of Evaluation of Activities.</small>
                            <?php endif; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
        $checklistModel = new \App\Models\OrganizationChecklistModel();
        if ($checklistModel->isComplete($checklist)):
            ?>
            <div class="certificate-section">
                <h3 style="color: #2c3e50;">Accreditation Complete</h3>
                <p class="muted">This organization has fulfilled all requirements. The Certificate of Accreditation is now
                    available.</p>
                <div class="cert-btn-group">
                    <a href="<?= base_url('admin/organizations/certificate/' . $organization['id'] . '/' . $academic_year) ?>"
                        class="btn btn-primary">⬇ Download Certificate PDF</a>
                    <a href="<?= base_url('admin/organizations/certificate/print/' . $organization['id'] . '/' . $academic_year) ?>"
                        target="_blank" class="btn btn-secondary" style="background: #34495e; color: white;">⎙ Print
                        Certificate</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        let currentCsrfHash = '<?= csrf_hash() ?>';
        const csrfHeader = '<?= csrf_header() ?>';

        async function updateChecklist(field, value) {
            const checkbox = document.getElementById('chk_' + field);
            const itemDiv = document.getElementById('item_' + field);

            try {
                const response = await fetch('<?= base_url("admin/organizations/checklist/" . $organization["id"]) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        [csrfHeader]: currentCsrfHash
                    },
                    body: `field=${field}&value=${value ? 1 : 0}&academic_year=<?= $academic_year ?>`
                });

                const result = await response.json();

                if (result.status === 'success') {
                    // Update CSRF hash for next request
                    currentCsrfHash = result.csrf;

                    if (value) {
                        itemDiv.classList.add('completed');
                    } else {
                        itemDiv.classList.remove('completed');
                    }
                } else {
                    alert('Failed to update checklist: ' + (result.message || 'Unknown error'));
                    checkbox.checked = !value;
                    if (result.csrf) currentCsrfHash = result.csrf;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating the checklist.');
                checkbox.checked = !value;
            }
        }
    </script>
</body>

</html>