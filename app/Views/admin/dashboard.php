<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - USG Accreditation</title>
    <link rel="icon" href="<?= base_url('webco.png') ?>" type="image/png">
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
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

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-card.blue {
            border-left: 4px solid #3498db;
        }

        .stat-card.green {
            border-left: 4px solid #2ecc71;
        }

        .stat-card.orange {
            border-left: 4px solid #f39c12;
        }

        .stat-card.red {
            border-left: 4px solid #e74c3c;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .menu-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            text-decoration: none;
            color: #2c3e50;
            transition: transform 0.2s;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .menu-card .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .menu-card h3 {
            font-size: 18px;
        }

        .recent-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .recent-section h2 {
            margin-bottom: 20px;
            color: #2c3e50;
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
    <nav class="navbar">
        <h1>USG Accreditation System - Admin</h1>
        <div class="navbar-right">
            <div class="user-info">
                <span>Welcome, <?= session()->get('username') ?></span>
            </div>
            <a href="<?= base_url('logout') ?>" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <h3>Total Organizations</h3>
                <div class="number"><?= $total_organizations ?></div>
            </div>
            <div class="stat-card green">
                <h3>Total Documents</h3>
                <div class="number"><?= $total_documents ?></div>
            </div>
            <div class="stat-card orange">
                <h3>Pending Reviews</h3>
                <div class="number"><?= $pending_documents ?></div>
            </div>
            <div class="stat-card red">
                <h3>Active Year</h3>
                <div class="number"><?= date('Y') ?></div>
            </div>
        </div>

        <!-- Quick Menu -->
        <div class="menu-grid">
            <a href="<?= base_url('admin/organizations') ?>" class="menu-card">
                <div class="icon">🏢</div>
                <h3>Manage Organizations</h3>
            </a>
            <a href="<?= base_url('admin/documents') ?>" class="menu-card">
                <div class="icon">📄</div>
                <h3>Review Documents</h3>
            </a>
            <a href="<?= base_url('admin/statistics') ?>" class="menu-card">
                <div class="icon">📊</div>
                <h3>View Statistics</h3>
            </a>
        </div>

        <!-- Recent Submissions -->
        <div class="recent-section">
            <h2>Recent Document Submissions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Organization</th>
                        <th>Document Type</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_submissions)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">No submissions yet</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_submissions as $submission): ?>
                            <tr>
                                <td><?= esc($submission['org_name']) ?></td>
                                <td><?= esc(str_replace('_', ' ', ucwords($submission['document_type']))) ?></td>
                                <td><?= esc($submission['document_title']) ?></td>
                                <td><?= date('M d, Y', strtotime($submission['created_at'])) ?></td>
                                <td>
                                    <span class="badge badge-<?= $submission['status'] ?>">
                                        <?= ucfirst($submission['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/documents/view/' . $submission['id']) ?>"
                                        style="color: #3498db; text-decoration: none;">View</a>
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