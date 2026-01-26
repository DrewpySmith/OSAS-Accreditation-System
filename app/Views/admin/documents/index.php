<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            align-items: end;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            color: #2c3e50;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 14px;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>USG Accreditation - Documents</h1>
        <div>
            <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <a href="<?= base_url('admin/organizations') ?>">Organizations</a>
            <a href="<?= base_url('admin/documents') ?>">Documents</a>
            <a href="<?= base_url('admin/statistics') ?>">Statistics</a>
            <a href="<?= base_url('logout') ?>">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0 0 10px 0;">Document Submissions</h2>
                <p style="margin: 0; color: #7f8c8d;">Filter by organization, status, or campus, then view individual
                    submissions.</p>
            </div>
            <a href="<?= base_url('admin/documents/print?' . http_build_query($selected_filters ?? [])) ?>"
                target="_blank" class="btn btn-success" style="background: #27ae60; color: white;">
                ⎙ Print Reports
            </a>
        </div>

        <div class="card">
            <form method="GET" action="<?= base_url('admin/documents') ?>">
                <div class="filters">
                    <div>
                        <label for="organization_id">Organization</label>
                        <select name="organization_id" id="organization_id">
                            <option value="">All</option>
                            <?php foreach (($organizations ?? []) as $org): ?>
                                <option value="<?= esc($org['id']) ?>" <?= (($selected_filters['organization_id'] ?? '') == $org['id']) ? 'selected' : '' ?>>
                                    <?= esc($org['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="campus">Campus</label>
                        <select name="campus" id="campus">
                            <option value="">All</option>
                            <?php foreach (($campuses ?? []) as $campus): ?>
                                <option value="<?= esc($campus) ?>" <?= (($selected_filters['campus'] ?? '') === $campus) ? 'selected' : '' ?>>
                                    <?= esc($campus) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="">All</option>
                            <?php foreach (['pending', 'reviewed', 'approved', 'rejected'] as $st): ?>
                                <option value="<?= esc($st) ?>" <?= (($selected_filters['status'] ?? '') === $st) ? 'selected' : '' ?>>
                                    <?= ucfirst($st) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="document_type">Document Type</label>
                        <select name="document_type" id="document_type">
                            <option value="">All Types</option>
                            <?php foreach (($document_types ?? []) as $val => $label): ?>
                                <option value="<?= esc($val) ?>" <?= (($selected_filters['document_type'] ?? '') === $val) ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="academic_year">Academic Year</label>
                        <select name="academic_year" id="academic_year">
                            <option value="">All Years</option>
                            <?php foreach (($academic_years ?? []) as $ay): ?>
                                <option value="<?= esc($ay['year']) ?>" <?= (($selected_filters['academic_year'] ?? '') === $ay['year']) ? 'selected' : '' ?>>
                                    A.Y. <?= esc($ay['year']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="grid-column: span 1;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Apply</button>
                    </div>
                    <div style="grid-column: span 1;">
                        <a href="<?= base_url('admin/documents') ?>" class="btn btn-secondary"
                            style="width: 100%; text-align: center;">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Organization</th>
                        <th>Campus</th>
                        <th>Academic Year</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding: 30px; color: #7f8c8d;">No documents found for
                                the selected filters.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?= esc($doc['id']) ?></td>
                                <td><?= esc($doc['document_title']) ?></td>
                                <td><?= esc($document_types[$doc['document_type']] ?? str_replace('_', ' ', ucwords($doc['document_type']))) ?>
                                </td>
                                <td><?= esc($doc['org_name'] ?? '') ?></td>
                                <td><span style="color: #27ae60; font-weight: 500;"><?= esc($doc['campus'] ?? 'N/A') ?></span>
                                </td>
                                <td><?= esc($doc['academic_year'] ?? '') ?></td>
                                <td>
                                    <?php $st = $doc['status'] ?? 'pending'; ?>
                                    <span class="badge badge-<?= esc($st) ?>"><?= ucfirst(esc($st)) ?></span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a class="btn btn-primary btn-sm"
                                            href="<?= base_url('admin/documents/view/' . $doc['id']) ?>">View</a>
                                        <a class="btn btn-secondary btn-sm"
                                            href="<?= base_url('admin/documents/download/' . $doc['id']) ?>">Download</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>