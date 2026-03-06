<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .header h3 {
            margin: 10px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background: #f0f0f0;
        }
        .text-right {
            text-align: right;
        }
        .signatures {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 40px 30px 5px 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>Republic of the Philippines</p>
        <h3>SULTAN KUDARAT STATE UNIVERSITY</h3>
        <p>ACCESS, EJC Montilla, 9800 City of Tacurong</p>
        <p>Province of Sultan Kudarat</p>
        <h3>PROGRAM OF EXPENDITURES</h3>
        <p>A.Y. <?= esc($academic_year) ?></p>
        <p style="margin: 10px 0;"><u><?= esc($organization['name']) ?></u></p>
        <p>(Organization)</p>
    </div>

    <h4>A. Expected Collections</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Type of Fee</th>
                <th style="width: 15%;">Amount</th>
                <th style="width: 20%;">Frequency of Collection</th>
                <th style="width: 15%;">Number of Students</th>
                <th style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($expenditures)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">No expenditure entries</td>
                </tr>
            <?php else: ?>
                <?php foreach ($expenditures as $exp): ?>
                    <tr>
                        <td><?= esc($exp['fee_type']) ?></td>
                        <td class="text-right">₱ <?= number_format($exp['amount'], 2) ?></td>
                        <td><?= esc($exp['frequency']) ?></td>
                        <td class="text-right"><?= number_format($exp['number_of_students']) ?></td>
                        <td class="text-right">₱ <?= number_format($exp['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Grand Total</th>
                <th class="text-right">₱ <?= number_format($grand_total, 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <p>Prepared by:</p>
            <div class="signature-line"></div>
            <p>Head of Organization</p>
        </div>
        <div class="signature-box">
            <p>Approved:</p>
            <div class="signature-line"></div>
            <p>Adviser</p>
        </div>
    </div>
</body>
</html>