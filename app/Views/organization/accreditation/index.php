<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accreditation Status - <?= esc($organization['name']) ?></title>
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
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .card {
            background: white;
            padding: 2.5rem;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .page-header h2 {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1a202c;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
        }

        .ay-badge {
            display: inline-block;
            background: #f0fdf4;
            color: #166534;
            padding: 0.25rem 1rem;
            border-radius: 1rem;
            border: 1px solid #bbf7d0;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .progress-box {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid #edf2f7;
            margin-bottom: 2.5rem;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 0.75rem;
        }

        .progress-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .progress-percent {
            font-size: 1.5rem;
            font-weight: 800;
            color: #2f855a;
        }

        .progress-bar-bg {
            background: #e2e8f0;
            height: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .progress-bar-fill {
            background: linear-gradient(90deg, #38a169 0%, #2f855a 100%);
            height: 100%;
            border-radius: 0.5rem;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .checklist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1rem;
        }

        .checklist-item {
            display: flex;
            align-items: center;
            padding: 1.25rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            gap: 1rem;
            transition: all 0.2s;
        }

        .checklist-item.verified {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .status-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .status-icon.pending {
            background: #f7fafc;
            color: #cbd5e0;
            border: 2px solid #edf2f7;
        }

        .status-icon.verified {
            background: #2f855a;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(47, 133, 90, 0.4);
        }

        .item-label {
            font-size: 0.9375rem;
            font-weight: 600;
            color: #4a5568;
        }

        .checklist-item.verified .item-label {
            color: #234e33;
        }

        .cert-card {
            margin-top: 3rem;
            padding: 3rem;
            border-radius: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cert-card.complete {
            background: linear-gradient(135deg, #f0fff4 0%, #ffffff 100%);
            border: 2px solid #bbf7d0;
        }

        .cert-card.not-complete {
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
        }

        .cert-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }

        .cert-title {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #2d3748;
        }

        .cert-desc {
            font-size: 1.1rem;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2f855a;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(47, 133, 90, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(47, 133, 90, 0.4);
        }

        .btn-secondary {
            background: #4a5568;
            color: white;
        }

        .btn-secondary:hover {
            background: #2d3748;
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
            <a href="<?= base_url('logout') ?>" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="page-header">
                <h2>Accreditation Status</h2>
                <div class="ay-badge">A.Y. <?= esc($academic_year) ?></div>
            </div>

            <div class="progress-box">
                <div class="progress-label">
                    <span class="progress-title">Overall Completion</span>
                    <span class="progress-percent"><?= $progress ?>%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: <?= $progress ?>%"></div>
                </div>
            </div>

            <h3
                style="margin-bottom: 1.5rem; font-size: 1.125rem; font-weight: 700; color: #4a5568; text-transform: uppercase; letter-spacing: 0.05em;">
                Requirement Checklist</h3>
            <div class="checklist-grid">
                <?php
                $items = array_filter($document_types ?? [], function ($key) {
                    return $key !== 'other';
                }, ARRAY_FILTER_USE_KEY);
                foreach ($items as $field => $label):
                    $isVerified = isset($checklist[$field]) && $checklist[$field] == 1;
                    ?>
                    <div class="checklist-item <?= $isVerified ? 'verified' : '' ?>">
                        <div class="status-icon <?= $isVerified ? 'verified' : 'pending' ?>">
                            <?= $isVerified ? '✓' : '•' ?>
                        </div>
                        <span class="item-label"><?= $label ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($is_complete): ?>
                <div class="cert-card complete">
                    <div class="cert-icon">🏆</div>
                    <h3 class="cert-title">Congratulations!</h3>
                    <p class="cert-desc">
                        Your organization is officially accredited for A.Y. <?= esc($academic_year) ?>.
                        You can now download or print your official certificate.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="<?= base_url('organization/accreditation/download/' . $academic_year) ?>"
                            class="btn btn-primary">
                            ⬇ Download PDF
                        </a>
                        <a href="<?= base_url('organization/accreditation/print/' . $academic_year) ?>" target="_blank"
                            class="btn btn-secondary">
                            ⎙ Print Certificate
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="cert-card not-complete">
                    <div class="cert-icon">🔒</div>
                    <h3 class="cert-title">Certificate Locked</h3>
                    <p class="cert-desc">
                        Complete all the requirements above to unlock your Certificate of Accreditation.
                        Items are verified by USG Admin upon submission.
                    </p>
                    <div
                        style="font-weight: 800; color: #718096; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        Required: 100% • Current: <?= $progress ?>%
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

</html>