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
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }

        .seal {
            width: 80px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #ccc;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #888;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }

        .print-btn {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">⎙ Print Report</button>
        <button onclick="window.close()" class="print-btn" style="background: #666;">Close Window</button>
    </div>

    <div class="header">
        <img src="<?= base_url('SKSU_Official_Seal.png') ?>" class="seal" alt="Seal">
        <div class="title">Sultan Kudarat State University</div>
        <div class="title">University Student Government</div>
        <div class="subtitle">
            <?= esc($title) ?>
        </div>
        <?php if (!empty($selected_campus)): ?>
            <div style="font-weight: bold; color: #27ae60;">CAMPUS:
                <?= esc($selected_campus) ?>
            </div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Document Title</th>
                <th>Type</th>
                <th>Organization</th>
                <?php if (empty($selected_campus)): ?>
                    <th>Campus</th>
                <?php endif; ?>
                <th>Academic Year</th>
                <th>Status</th>
                <th>Submitted Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($documents)): ?>
                <tr>
                    <td colspan="<?= empty($selected_campus) ? '8' : '7' ?>" style="text-align: center; padding: 20px;">No
                        document submissions found for the selected filters.</td>
                </tr>
            <?php else: ?>
                <?php $i = 1;
                foreach ($documents as $doc): ?>
                    <tr>
                        <td>
                            <?= $i++ ?>
                        </td>
                        <td><strong>
                                <?= esc($doc['document_title']) ?>
                            </strong></td>
                        <td>
                            <?= esc(str_replace('_', ' ', ucwords($doc['document_type']))) ?>
                        </td>
                        <td>
                            <?= esc($doc['org_name'] ?? '') ?>
                        </td>
                        <?php if (empty($selected_campus)): ?>
                            <td>
                                <?= esc($doc['campus'] ?? 'N/A') ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?= esc($doc['academic_year'] ?? '') ?>
                        </td>
                        <td>
                            <?= ucfirst(esc($doc['status'])) ?>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($doc['created_at'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Generated by Admin on
        <?= date('F d, Y h:i A') ?>
    </div>
</body>

</html>