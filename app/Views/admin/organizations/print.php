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
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
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
            margin-bottom: 20px;
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
            font-size: 13px;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #888;
            text-align: right;
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
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">⎙ Print This Page</button>
        <button onclick="window.close()" class="print-btn" style="background: #666;">Close</button>
    </div>

    <div class="header">
        <img src="<?= base_url('SKSU_Official_Seal.png') ?>" class="seal" alt="Seal">
        <div class="title">Sultan Kudarat State University</div>
        <div class="title">University Student Government</div>
        <div class="subtitle">
            <?= esc($title) ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Organization Name</th>
                <th>Acronym</th>
                <th>Campus</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($organizations)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No organizations found for this campus.</td>
                </tr>
            <?php else: ?>
                <?php $i = 1;
                foreach ($organizations as $org): ?>
                    <tr>
                        <td>
                            <?= $i++ ?>
                        </td>
                        <td><strong>
                                <?= esc($org['name']) ?>
                            </strong></td>
                        <td>
                            <?= esc($org['acronym']) ?>
                        </td>
                        <td>
                            <?= esc($org['campus']) ?>
                        </td>
                        <td>
                            <?= ucfirst($org['status']) ?>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($org['created_at'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Generated on
        <?= date('F d, Y h:i A') ?>
    </div>

    <script>
        // Auto-open print dialog
        window.onload = function () {
            // setTimeout(() => window.print(), 500);
        }
    </script>
</body>

</html>