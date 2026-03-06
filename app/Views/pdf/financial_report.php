<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header p {
            margin: 2px 0;
        }
        .header h3 {
            margin: 8px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .text-right {
            text-align: right;
        }
        .section-title {
            font-weight: bold;
            margin: 15px 0 8px 0;
        }
        .signatures {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 15px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 30px 20px 5px 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>Republic of the Philippines</p>
        <h3>SULTAN KUDARAT STATE UNIVERSITY</h3>
        <p>ACCESS, EJC Montilla, 9800 City of Tacurong</p>
        <p>Province of Sultan Kudarat</p>
        <h3>FINANCIAL REPORTS</h3>
        <p>A.Y. <?= esc($report['academic_year']) ?></p>
        <p style="margin: 8px 0;"><u><?= esc($organization['name'] ?? 'Organization') ?></u></p>
        <p>(Organization)</p>
    </div>

    <div class="section-title">1. Summary of Collection</div>
    <table>
        <thead>
            <tr>
                <th style="width: 60%;">Types of Collection</th>
                <th style="width: 40%;">Total Amount Collected</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $collections = is_array($report['collections']) ? $report['collections'] : json_decode($report['collections'] ?? '[]', true);
            if (empty($collections)): ?>
                <tr>
                    <td colspan="2" style="text-align: center;">No collections recorded</td>
                </tr>
            <?php else: ?>
                <?php foreach ($collections as $collection): ?>
                    <tr>
                        <td><?= esc($collection['type']) ?></td>
                        <td class="text-right">₱ <?= number_format($collection['amount'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Grand Total</th>
                <th class="text-right">₱ <?= number_format($report['total_collection'], 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">2. Summary of Expenses</div>
    <table>
        <thead>
            <tr>
                <th style="width: 60%;">Activity/Project/Program</th>
                <th style="width: 40%;">Total Expenses</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $expenses = is_array($report['expenses']) ? $report['expenses'] : json_decode($report['expenses'] ?? '[]', true);
            if (empty($expenses)): ?>
                <tr>
                    <td colspan="2" style="text-align: center;">No expenses recorded</td>
                </tr>
            <?php else: ?>
                <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td><?= esc($expense['activity']) ?></td>
                        <td class="text-right">₱ <?= number_format($expense['amount'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Grand Total</th>
                <th class="text-right">₱ <?= number_format($report['total_expenses'], 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">3. Cash on Bank: ₱ <?= number_format($report['cash_on_bank'], 2) ?></div>

    <div class="section-title">4. Cash on Hand</div>
    <table>
        <tr>
            <td style="width: 70%;">1. Total Collection</td>
            <td class="text-right">₱ <?= number_format($report['total_collection'], 2) ?></td>
        </tr>
        <tr>
            <td>2. Total Expenses</td>
            <td class="text-right">₱ <?= number_format($report['total_expenses'], 2) ?></td>
        </tr>
        <tr>
            <td>3. Cash on Bank</td>
            <td class="text-right">₱ <?= number_format($report['cash_on_bank'], 2) ?></td>
        </tr>
        <tr>
            <td>4. Cash on Hand (1 – 2 – 3 =)</td>
            <td class="text-right">₱ <?= number_format($report['cash_on_hand'], 2) ?></td>
        </tr>
        <tr>
            <th>5. Total Remaining Fund (3 + 4 =)</th>
            <th class="text-right">₱ <?= number_format($report['total_remaining_fund'], 2) ?></th>
        </tr>
    </table>

    <p style="font-size: 10px; margin-top: 8px;">*Note: Please attach photocopy of organization passbook.</p>

    <div class="signatures">
        <div class="signature-box">
            <p>Prepared by:</p>
            <div class="signature-line"></div>
            <p><?= esc($report['treasurer_name'] ?? '_____________________') ?></p>
            <p>Treasurer</p>
        </div>
        <div class="signature-box">
            <p>Reviewed by:</p>
            <div class="signature-line"></div>
            <p><?= esc($report['auditor_name'] ?? '_____________________') ?></p>
            <p>Auditor</p>
        </div>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><?= esc($report['head_name'] ?? '_____________________') ?></p>
            <p>Head of Organization</p>
        </div>
        <div class="signature-box">
            <p>Approved:</p>
            <div class="signature-line"></div>
            <p><?= esc($report['adviser_name'] ?? '_____________________') ?></p>
            <p>Adviser</p>
        </div>
    </div>

    <p style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">Page 2 of 2</p>
</body>
</html>