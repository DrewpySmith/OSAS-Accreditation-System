<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #1a1a1a;
            font-size: 11px;
            line-height: 1.4;
            background: #fff;
            padding: 12mm 16mm;
        }
        .page {
            padding: 0 18px;
        }
        .official-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0 6px 0;
        }
        .header-logos {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .header-logos img {
            height: 52px;
            width: auto;
            object-fit: contain;
        }
        .header-logos .bagong {
            height: 48px;
        }
        .header-text {
            flex: 1;
            text-align: center;
            line-height: 1.15;
        }
        .header-text .republic {
            font-size: 10px;
            font-style: italic;
            color: #111;
        }
        .header-text .univ {
            font-size: 14px;
            font-weight: 800;
            color: #15803d;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .header-text .address {
            font-size: 8.5px;
            color: #333;
        }
        .header-text .address em {
            font-style: normal;
            color: #6b7280;
        }
        .contact-bar {
            display: flex;
            justify-content: center;
            gap: 14px;
            font-family: Arial, sans-serif;
            font-size: 6.5px;
            color: #6b7280;
            padding: 3px 0 8px 0;
            border-bottom: 1px solid #e5e7eb;
            letter-spacing: 0.2px;
        }
        .doc-title {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin: 14px 0 16px 0;
            color: #111;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 9.5px;
        }
        table th,
        table td {
            border: 1px solid #9ca3af;
            padding: 5px 6px;
            text-align: left;
        }
        table th {
            background: #15803d;
            color: #fff;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
        }
        table td { vertical-align: top; }
        table tbody tr:nth-child(even) td { background: #f9fafb; }
        .footer-meta {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #6b7280;
        }
        .page-number {
            text-align: right;
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #6b7280;
            margin-top: 10px;
        }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        .print-btn {
            background: #15803d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin-right: 8px;
        }
        .no-print { text-align: right; padding: 10px 0; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">⎙ Print</button>
        <button onclick="window.close()" class="print-btn" style="background:#6b7280;">Close</button>
    </div>

    <div class="page">
        <div class="official-header">
            <div class="header-logos">
                <img class="bagong" src="<?= base_url('Bagong_Pilipinas_Logo.svg') ?>" alt="Bagong Pilipinas">
                <img src="<?= base_url('SKSU_Official_Seal.png') ?>" alt="SKSU Seal">
            </div>
            <div class="header-text">
                <div class="republic">Republic of the Philippines</div>
                <div class="univ">Sultan Kudarat State University</div>
                <div class="address">EJC Montilla, City of Tacurong 9800<br>Province of Sultan Kudarat</div>
            </div>
            <div style="width:52px;"></div>
        </div>
        <div class="contact-bar">
            <span>https://www.sksu.edu.ph</span>
            <span>•</span>
            <span>officeofthepresident@sksu.edu.ph</span>
            <span>•</span>
            <span>(064) 200-7336</span>
        </div>

        <div class="doc-title"><?= esc($title) ?></div>

        <table>
            <thead>
                <tr>
                    <th style="width:32px;">No.</th>
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
                        <td colspan="6" style="text-align:center; padding:16px;">No organizations found for this campus.</td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1; foreach ($organizations as $org): ?>
                        <tr>
                            <td style="text-align:center;"><?= $i++ ?></td>
                            <td><strong><?= esc($org['name']) ?></strong></td>
                            <td style="text-align:center;"><?= esc($org['acronym']) ?></td>
                            <td style="text-align:center;"><?= esc($org['campus']) ?></td>
                            <td style="text-align:center;"><?= ucfirst($org['status']) ?></td>
                            <td style="text-align:center;"><?= date('M d, Y', strtotime($org['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer-meta">
            <span>Generated by OSAS — <?= date('F d, Y h:i A') ?></span>
            <span><?= esc($title) ?></span>
        </div>
        <div class="page-number">Page 1 of 1</div>
    </div>
</body>
</html>
