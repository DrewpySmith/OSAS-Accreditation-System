<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Statistics - Admin</title>
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
        .muted { color: #7f8c8d; }
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
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>USG Accreditation - Statistics</h1>
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
            <a href="<?= base_url('admin/statistics') ?>" class="btn btn-secondary">← Back</a>
            <h2 style="margin: 15px 0 5px 0;"><?= esc($organization['name'] ?? '') ?></h2>
            <?php if (!empty($organization['acronym'])): ?>
                <div class="muted"><?= esc($organization['acronym']) ?></div>
            <?php endif; ?>
        </div>

        <div class="grid">
            <div class="card">
                <h3 style="margin-top: 0;">Available Academic Years</h3>
                <?php if (empty($years)): ?>
                    <p class="muted">No financial report years found.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($years as $yr): ?>
                            <li><?= esc($yr) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3 style="margin-top: 0;">Quick Links</h3>
                <?php if (!empty($organization['id'])): ?>
                    <p>
                        <a class="btn btn-primary" href="<?= base_url('admin/documents?organization_id=' . $organization['id']) ?>">View Documents</a>
                    </p>
                    <?php
                        $calendarFirstYear = !empty($calendar_years) ? $calendar_years[0] : null;
                        $firstYear = !empty($years) ? $years[0] : null;
                    ?>
                    <?php if (!empty($calendarFirstYear) || !empty($firstYear)): ?>
                        <p class="muted" style="margin: 10px 0 0 0;">Downloads/Prints will use the first listed academic year by default.</p>
                        <p style="margin-top: 10px;">
                            <?php if (!empty($calendarFirstYear)): ?>
                                <a class="btn btn-secondary" href="<?= base_url('admin/calendar-activities/download/' . $organization['id'] . '/' . $calendarFirstYear) ?>">Download Calendar</a>
                                <a class="btn btn-secondary" href="<?= base_url('admin/calendar-activities/print/' . $organization['id'] . '/' . $calendarFirstYear) ?>" target="_blank">Print Calendar</a>
                            <?php else: ?>
                                <span class="muted">No Calendar Activities academic years found.</span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <?php if (!empty($firstYear)): ?>
                                <a class="btn btn-secondary" href="<?= base_url('admin/program-expenditures/download/' . $organization['id'] . '/' . $firstYear) ?>">Download Program Expenditure</a>
                                <a class="btn btn-secondary" href="<?= base_url('admin/program-expenditures/print/' . $organization['id'] . '/' . $firstYear) ?>" target="_blank">Print Program Expenditure</a>
                            <?php else: ?>
                                <span class="muted">No Program Expenditures academic years found.</span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Commitment Forms</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Officer</th>
                        <th>Position</th>
                        <th>Academic Year</th>
                        <th>Signed Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commitment_forms)): ?>
                        <tr>
                            <td colspan="7" class="muted" style="text-align:center; padding: 25px;">No commitment forms found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($commitment_forms as $cf): ?>
                            <tr>
                                <td><?= esc($cf['id'] ?? '') ?></td>
                                <td><?= esc($cf['officer_name'] ?? '') ?></td>
                                <td><?= esc($cf['position'] ?? '') ?></td>
                                <td><?= esc($cf['academic_year'] ?? '') ?></td>
                                <td><?= !empty($cf['signed_date']) ? date('M d, Y', strtotime($cf['signed_date'])) : '' ?></td>
                                <td><?= esc($cf['status'] ?? '') ?></td>
                                <td>
                                    <a class="btn btn-primary" href="<?= base_url('admin/commitment-forms/download/' . $cf['id']) ?>">Download</a>
                                    <a class="btn btn-secondary" href="<?= base_url('admin/commitment-forms/print/' . $cf['id']) ?>" target="_blank">Print</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Uploaded Financial Reports (Approved)</h3>
            <?php if (empty($approved_financial_report_documents)): ?>
                <p class="muted">No approved uploaded financial reports found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Academic Year</th>
                            <th>Title</th>
                            <th>Submitted By</th>
                            <th>Submitted Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approved_financial_report_documents as $doc): ?>
                            <tr>
                                <td><?= esc($doc['academic_year'] ?? '') ?></td>
                                <td><?= esc($doc['document_title'] ?? '') ?></td>
                                <td><?= esc($doc['submitted_by_name'] ?? '') ?></td>
                                <td><?= !empty($doc['created_at']) ? date('M d, Y', strtotime($doc['created_at'])) : '' ?></td>
                                <td>
                                    <?php if (!empty($doc['id'])): ?>
                                        <a class="btn btn-primary" href="<?= base_url('admin/documents/view/' . $doc['id']) ?>">View</a>
                                        <a class="btn btn-secondary" href="<?= base_url('admin/documents/download/' . $doc['id']) ?>">Download</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Financial Reports (Yearly)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Total Collection</th>
                        <th>Total Expenses</th>
                        <th>Remaining Fund</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($financial_reports)): ?>
                        <tr>
                            <td colspan="6" class="muted" style="text-align:center; padding: 25px;">No financial reports found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($financial_reports as $fr): ?>
                            <tr>
                                <td><?= esc($fr['academic_year'] ?? '') ?></td>
                                <td><?= esc($fr['total_collection'] ?? 0) ?></td>
                                <td><?= esc($fr['total_expenses'] ?? 0) ?></td>
                                <td><?= esc($fr['total_remaining_fund'] ?? 0) ?></td>
                                <td><?= esc($fr['status'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($fr['id'])): ?>
                                        <a class="btn btn-primary" href="<?= base_url('admin/financial-reports/download/' . $fr['id']) ?>">Download</a>
                                        <a class="btn btn-secondary" href="<?= base_url('admin/financial-reports/print/' . $fr['id']) ?>" target="_blank">Print</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Program Expenditure Summary (Yearly)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Grand Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenditure_summary)): ?>
                        <tr>
                            <td colspan="3" class="muted" style="text-align:center; padding: 25px;">No expenditure summary found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenditure_summary as $row): ?>
                            <tr>
                                <td><?= esc($row['academic_year'] ?? '') ?></td>
                                <td><?= esc($row['grand_total'] ?? 0) ?></td>
                                <td>
                                    <?php if (!empty($organization['id']) && !empty($row['academic_year'])): ?>
                                        <a class="btn btn-primary" href="<?= base_url('admin/program-expenditures/download/' . $organization['id'] . '/' . $row['academic_year']) ?>">Download</a>
                                        <a class="btn btn-secondary" href="<?= base_url('admin/program-expenditures/print/' . $organization['id'] . '/' . $row['academic_year']) ?>" target="_blank">Print</a>
                                    <?php endif; ?>
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
