<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header p {
            margin: 2px 0;
        }
        .header h3 {
            margin: 10px 0;
        }
        .content {
            line-height: 2;
            text-align: justify;
        }
        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding: 0 10px;
        }
        .signature-section {
            margin-top: 60px;
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            width: 300px;
            margin: 50px auto 10px auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>Republic of the Philippines</p>
        <h3>SULTAN KUDARAT STATE UNIVERSITY</h3>
        <p>ACCESS, EJC Montilla, 9800 City of Tacurong</p>
        <p>Province of Sultan Kudarat</p>
        <h3>COMMITMENT FORM</h3>
    </div>

    <div class="content">
        <p>
            I <span class="underline"><?= esc($form['officer_name']) ?></span>
            hereby committed to take my responsibilities and duties as the newly elected
            <span class="underline"><?= esc($form['position']) ?></span>
            of the <span class="underline"><?= esc($form['organization_name']) ?></span>
        </p>
        <p>
            AY <span class="underline"><?= esc($form['academic_year']) ?></span>.
            I will render the best service I can give for the welfare of the said
            organization, my fellow students, and University. I will respectfully abide
            the constitution and By-Laws of the Republic of the Philippines and the rules
            and regulations of Sultan Kudarat State University.
        </p>
        <p style="text-align: center; margin-top: 30px;">
            So help me God.
        </p>
    </div>

    <div class="signature-section">
        <p>Signed this <?= date('jS', strtotime($form['signed_date'])) ?> day of <?= date('F', strtotime($form['signed_date'])) ?>.</p>
        <div class="signature-line"></div>
        <p>Signature</p>
    </div>
</body>
</html>