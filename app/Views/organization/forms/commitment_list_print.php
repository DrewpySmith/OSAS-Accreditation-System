<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        <?= esc($title) ?>
    </title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 20px;
        }

        .seal {
            width: 90px;
            margin-bottom: 10px;
        }

        .univ-name {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .dept-name {
            font-size: 18px;
            margin: 5px 0;
        }

        .doc-title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 20px;
            text-transform: uppercase;
            color: #27ae60;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }

        table th {
            background-color: #f4fdf4;
            color: #27ae60;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
        }

        table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 50px;
            font-weight: bold;
            padding-top: 5px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            button {
                display: none;
            }
        }

        .print-btn {
            background: #27ae60;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 30px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Print This List</button>
        <button onclick="window.close()" class="print-btn" style="background: #666;">Close Window</button>
    </div>

    <div class="header">
        <img src="<?= base_url('SKSU_Official_Seal.png') ?>" class="seal" alt="SKSU Seal">
        <div class="univ-name">Sultan Kudarat State University</div>
        <div class="dept-name">University Student Government</div>
        <div class="doc-title">
            <?= esc($title) ?>
        </div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Organization:</span>
            <span>
                <?= esc($organization['name']) ?> (
                <?= esc($organization['acronym']) ?>)
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Academic Year:</span>
            <span>
                <?= esc($academic_year) ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Officers:</span>
            <span>
                <?= count($forms) ?>
            </span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No.</th>
                <th>Full Name</th>
                <th>Position</th>
                <th>Date Signed</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($forms)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #666;">No officers have submitted
                        commitment forms for this academic year yet.</td>
                </tr>
            <?php else: ?>
                <?php $i = 1;
                foreach ($forms as $f): ?>
                    <tr>
                        <td>
                            <?= $i++ ?>
                        </td>
                        <td><strong>
                                <?= esc($f['officer_name']) ?>
                            </strong></td>
                        <td>
                            <?= esc($f['position']) ?>
                        </td>
                        <td>
                            <?= date('F d, Y', strtotime($f['signed_date'])) ?>
                        </td>
                        <td><span style="color: #27ae60; font-weight: bold;">Certified</span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <div class="signature-line">Prepared By</div>
            <div style="font-size: 14px; margin-top: 5px;">Organization Secretary</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Noted By</div>
            <div style="font-size: 14px; margin-top: 5px;">Organization President</div>
        </div>
    </div>

    <div style="margin-top: 40px; font-size: 11px; color: #888; text-align: center;">
        Generated on
        <?= date('F d, Y h:i A') ?> via USG Accreditation System
    </div>
</body>

</html>