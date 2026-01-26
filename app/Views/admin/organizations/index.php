<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Organizations - Admin</title>
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
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            color: #2c3e50;
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
            background: #3498db;
            color: white;
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
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

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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


        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h2>Manage Organizations</h2>
            <div style="display: flex; gap: 10px; align-items: center;">
                <form action="<?= base_url('admin/organizations') ?>" method="get" id="filterForm">
                    <select name="campus" onchange="this.form.submit()"
                        style="padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                        <option value="">All Campuses</option>
                        <?php foreach ($campuses as $campus): ?>
                            <option value="<?= $campus ?>" <?= ($selected_campus == $campus) ? 'selected' : '' ?>>
                                <?= $campus ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="<?= base_url('admin/organizations/print' . (!empty($selected_campus) ? '?campus=' . $selected_campus : '')) ?>"
                    target="_blank" class="btn btn-success">
                    ⎙ Print List
                </a>
                <a href="<?= base_url('admin/organizations/create') ?>" class="btn btn-primary">
                    + Create New Organization
                </a>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Acronym</th>
                        <th>Campus</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($organizations)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px;">
                                No organizations found. Create one to get started!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($organizations as $org): ?>
                            <tr>
                                <td><?= $org['id'] ?></td>
                                <td><strong><?= esc($org['name']) ?></strong></td>
                                <td><?= esc($org['acronym']) ?></td>
                                <td><span style="color: #27ae60; font-weight: 500;"><?= esc($org['campus'] ?? 'N/A') ?></span>
                                </td>
                                <td><?= esc($org['description']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $org['status'] ?>">
                                        <?= ucfirst($org['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($org['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= base_url('admin/organizations/view/' . $org['id']) ?>"
                                            class="btn btn-success btn-sm">View</a>
                                        <a href="<?= base_url('admin/organizations/edit/' . $org['id']) ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <a href="<?= base_url('admin/organizations/delete/' . $org['id']) ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this organization?')">
                                            Delete
                                        </a>
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