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
        <h3>CALENDAR OF ACTIVITIES</h3>
        <p>A.Y. <?= esc($academic_year) ?></p>
        <p style="margin: 10px 0;"><u><?= esc($organization['name']) ?></u></p>
        <p>(Organization)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 35%;">Activity Title</th>
                <th style="width: 25%;">Responsible Person</th>
                <th style="width: 25%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($activities)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">No activities recorded</td>
                </tr>
            <?php else: ?>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($activity['activity_date'])) ?></td>
                        <td><?= esc($activity['activity_title']) ?></td>
                        <td><?= esc($activity['responsible_person']) ?></td>
                        <td><?= esc($activity['remarks']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <p>Prepared by:</p>
            <div class="signature-line"></div>
            <p><?= esc($signatory['head_name'] ?? '_____________________') ?></p>
            <p>Head of Organization</p>
        </div>
        <div class="signature-box">
            <p>Approved:</p>
            <div class="signature-line"></div>
            <p><?= esc($signatory['adviser_name'] ?? '_____________________') ?></p>
            <p>Adviser</p>
        </div>
    </div>
</body>
</html>