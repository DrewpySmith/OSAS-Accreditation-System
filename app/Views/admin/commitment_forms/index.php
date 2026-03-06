<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commitment Forms - Admin</title>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
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
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            align-items: end;
        }
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #3498db; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-sm { padding: 6px 10px; font-size: 14px; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
            text-align: left;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        tr:hover { background: #f8f9fa; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .muted { color: #7f8c8d; }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>USG Accreditation - Commitment Forms</h1>
        <div>
            <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <a href="<?= base_url('admin/organizations') ?>">Organizations</a>
            <a href="<?= base_url('admin/documents') ?>">Documents</a>
            <a href="<?= base_url('admin/statistics') ?>">Statistics</a>
            <a href="<?= base_url('logout') ?>">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2 style="margin: 0 0 10px 0;">Commitment Forms</h2>
            <p class="muted" style="margin: 0;">View and download commitment forms submitted by organizations.</p>
        </div>

        <div class="card">
            <?php $selectedOrg = service('request')->getGet('organization_id'); ?>
            <form method="GET" action="<?= base_url('admin/commitment-forms') ?>">
                <div class="filters">
                    <div>
                        <label for="organization_id">Organization</label>
                        <select name="organization_id" id="organization_id">
                            <option value="">All</option>
                            <?php foreach (($organizations ?? []) as $org): ?>
                                <option value="<?= esc($org['id']) ?>" <?= ($selectedOrg == $org['id']) ? 'selected' : '' ?>>
                                    <?= esc($org['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="<?= base_url('admin/commitment-forms') ?>" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Organization</th>
                        <th>Officer</th>
                        <th>Position</th>
                        <th>Academic Year</th>
                        <th>Signed Date</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($forms)): ?>
                        <tr>
                            <td colspan="9" class="muted" style="text-align:center; padding: 30px;">No commitment forms found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($forms as $f): ?>
                            <tr>
                                <td><?= esc($f['id']) ?></td>
                                <td><?= esc($f['org_name'] ?? '') ?></td>
                                <td><?= esc($f['officer_name'] ?? '') ?></td>
                                <td><?= esc($f['position'] ?? '') ?></td>
                                <td><?= esc($f['academic_year'] ?? '') ?></td>
                                <td><?= !empty($f['signed_date']) ? date('M d, Y', strtotime($f['signed_date'])) : '' ?></td>
                                <td><?= esc($f['status'] ?? '') ?></td>
                                <td><?= !empty($f['created_at']) ? date('M d, Y', strtotime($f['created_at'])) : '' ?></td>
                                <td>
                                    <div class="actions">
                                        <a class="btn btn-primary btn-sm" href="<?= base_url('admin/commitment-forms/download/' . $f['id']) ?>">Download</a>
                                        <a class="btn btn-secondary btn-sm" href="<?= base_url('admin/commitment-forms/print/' . $f['id']) ?>" target="_blank">Print</a>
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
