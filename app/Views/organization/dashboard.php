<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Dashboard - USG Accreditation</title>
    <link rel="icon" href="<?= base_url('webco.png') ?>" type="image/png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f0f2f5;
            color: #1a202c;
        }

        .navbar {
            background: #2f855a;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .welcome-card {
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .welcome-card h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .welcome-card p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .menu-tile {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .menu-tile:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #2f855a;
        }

        .menu-tile .icon-wrapper {
            width: 4rem;
            height: 4rem;
            background: #f0fdf4;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.25rem;
            transition: background 0.3s;
        }

        .menu-tile:hover .icon-wrapper {
            background: #2f855a;
            color: white;
        }

        .menu-tile h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2d3748;
        }

        .menu-tile p {
            font-size: 0.875rem;
            color: #718096;
            line-height: 1.5;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title::before {
            content: '';
            display: block;
            width: 4px;
            height: 1.5rem;
            background: #2f855a;
            border-radius: 2px;
        }

        .checklist-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
        }

        .checklist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }

        .checklist-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            gap: 1rem;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .checklist-item.verified {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .checklist-item .status-icon {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .status-icon.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-icon.verified {
            background: #22c55e;
            color: white;
        }

        .checklist-item-content {
            flex: 1;
        }

        .checklist-item-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #4a5568;
        }

        .checklist-item.verified .checklist-item-title {
            color: #166534;
        }

        .notification-bell {
            position: relative;
            font-size: 1.25rem;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e53e3e;
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            border: 2px solid #2f855a;
        }

        .btn-logout {
            padding: 0.5rem 1.25rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>USG Accreditation</h1>
        <div class="navbar-right">
            <a href="<?= base_url('organization/dashboard') ?>" class="nav-link">Dashboard</a>
            <a href="<?= base_url('organization/accreditation') ?>" class="nav-link">Accreditation</a>
            <a href="<?= base_url('organization/notifications') ?>" class="notification-bell nav-link">
                🔔
                <?php if (isset($unread_count) && $unread_count > 0): ?>
                    <span class="notification-badge"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= base_url('logout') ?>" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>Welcome, <?= session()->get('username') ?>!</h2>
            <p><?= $organization['name'] ?? 'Organization Dashboard' ?></p>
        </div>

        <h2 class="section-title">Application Menu</h2>
        <div class="menu-grid">
            <a href="<?= base_url('organization/submissions/upload') ?>" class="menu-tile">
                <div class="icon-wrapper">📤</div>
                <h3>Upload Docs</h3>
                <p>Submit accreditation requirements</p>
            </a>

            <a href="<?= base_url('organization/submissions') ?>" class="menu-tile">
                <div class="icon-wrapper">📋</div>
                <h3>Submissions</h3>
                <p>Track review status of files</p>
            </a>

            <a href="<?= base_url('organization/accreditation') ?>" class="menu-tile">
                <div class="icon-wrapper">📜</div>
                <h3>Status</h3>
                <p>View A.Y. accreditation progress</p>
            </a>

            <a href="<?= base_url('organization/commitment-form') ?>" class="menu-tile">
                <div class="icon-wrapper">📝</div>
                <h3>Commitment</h3>
                <p>Manage officer commitment forms</p>
            </a>

            <a href="<?= base_url('organization/calendar-activities') ?>" class="menu-tile">
                <div class="icon-wrapper">📅</div>
                <h3>Calendar</h3>
                <p>Plan organization activities</p>
            </a>

            <a href="<?= base_url('organization/program-expenditure') ?>" class="menu-tile">
                <div class="icon-wrapper">💰</div>
                <h3>Expenditures</h3>
                <p>Budget and fee proposals</p>
            </a>

            <a href="<?= base_url('organization/accomplishment-report') ?>" class="menu-tile">
                <div class="icon-wrapper">✅</div>
                <h3>Accomplishments</h3>
                <p>Document completed events</p>
            </a>

            <a href="<?= base_url('organization/financial-report') ?>" class="menu-tile">
                <div class="icon-wrapper">📊</div>
                <h3>Financials</h3>
                <p>Track collections & expenses</p>
            </a>

            <a href="<?= base_url('organization/financial-report/tracking') ?>" class="menu-tile">
                <div class="icon-wrapper">📈</div>
                <h3>Tracking</h3>
                <p>Real-time financial analytics</p>
            </a>
        </div>

        <h2 class="section-title">
            Checklist Status
            <span
                style="font-size: 0.875rem; background: #f0fdf4; color: #166534; padding: 0.25rem 0.75rem; border-radius: 1rem; border: 1px solid #bbf7d0;">
                A.Y. <?= esc($academic_year) ?>
            </span>
        </h2>
        <div class="checklist-container">
            <div class="checklist-grid">
                <?php
                $items = array_filter($document_types ?? [], function ($key) {
                    return $key !== 'other';
                }, ARRAY_FILTER_USE_KEY);

                foreach ($items as $field => $label):
                    $isCompleted = isset($checklist[$field]) && $checklist[$field] == 1;
                    ?>
                    <div class="checklist-item <?= $isCompleted ? 'verified' : '' ?>">
                        <div class="status-icon <?= $isCompleted ? 'verified' : 'pending' ?>">
                            <?= $isCompleted ? '✓' : '!' ?>
                        </div>
                        <div class="checklist-item-content">
                            <div class="checklist-item-title"><?= $label ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>