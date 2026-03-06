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
        .section {
            margin: 20px 0;
        }
        .section h4 {
            margin-bottom: 10px;
            background: #f0f0f0;
            padding: 8px;
            border-left: 4px solid #2c3e50;
        }
        .narrative {
            text-align: justify;
            line-height: 1.8;
            padding: 15px;
            border: 1px solid #ddd;
        }
        .file-list {
            padding: 10px;
            background: #f8f9fa;
        }
        .file-item {
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>Republic of the Philippines</p>
        <h3>SULTAN KUDARAT STATE UNIVERSITY</h3>
        <p>ACCESS, EJC Montilla, 9800 City of Tacurong</p>
        <p>Province of Sultan Kudarat</p>
        <h3>PARTS OF ACCOMPLISHMENT REPORTS</h3>
        <p style="margin: 15px 0;">A.Y. <?= esc($report['academic_year']) ?></p>
    </div>

    <div class="section">
        <h4>A. Activity Title</h4>
        <p style="padding: 10px; font-size: 14px; font-weight: 600;">
            <?= esc($report['activity_title']) ?>
        </p>
    </div>

    <div class="section">
        <h4>B. Narrative Report</h4>
        <div class="narrative">
            <?= nl2br(esc($report['narrative_report'])) ?>
        </div>
    </div>

    <div class="section">
        <h4>C. Pictorials</h4>
        <div class="file-list">
            <?php
            $pictorials = is_array($report['pictorials']) ? $report['pictorials'] : json_decode($report['pictorials'] ?? '[]', true);
            if (empty($pictorials)): ?>
                <p>No pictorials attached</p>
            <?php else: ?>
                <?php foreach ($pictorials as $index => $file): ?>
                    <div class="file-item">
                        <?= ($index + 1) ?>. <?= basename($file) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="section">
        <h4>D. Photocopy of Activity Designs</h4>
        <div class="file-list">
            <?php
            $designs = is_array($report['activity_designs']) ? $report['activity_designs'] : json_decode($report['activity_designs'] ?? '[]', true);
            if (empty($designs)): ?>
                <p>No activity designs attached</p>
            <?php else: ?>
                <?php foreach ($designs as $index => $file): ?>
                    <div class="file-item">
                        <?= ($index + 1) ?>. <?= basename($file) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="section">
        <h4>E. Sample Evaluation Sheets</h4>
        <div class="file-list">
            <?php
            $evaluations = is_array($report['evaluation_sheets']) ? $report['evaluation_sheets'] : json_decode($report['evaluation_sheets'] ?? '[]', true);
            if (empty($evaluations)): ?>
                <p>No evaluation sheets attached</p>
            <?php else: ?>
                <?php foreach ($evaluations as $index => $file): ?>
                    <div class="file-item">
                        <?= ($index + 1) ?>. <?= basename($file) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top: 50px; text-align: center;">
        <p style="font-size: 11px; color: #7f8c8d;">
            Generated on <?= date('F d, Y') ?>
        </p>
    </div>
</body>
</html>