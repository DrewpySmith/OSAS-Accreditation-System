<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Certificate of Accreditation</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            border: 20px solid #27ae60;
            position: relative;
        }

        .inner-border {
            border: 5px solid #d4af37;
            /* Gold */
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            background: #fff;
            text-align: center;
        }

        .header {
            margin-bottom: 30px;
        }

        .seal {
            width: 100px;
            margin-bottom: 10px;
        }

        .university-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
        }

        .office-name {
            font-size: 16px;
            color: #27ae60;
            margin: 5px 0;
            font-weight: bold;
        }

        .title {
            font-size: 48px;
            color: #2c3e50;
            margin: 30px 0;
            font-family: 'Times New Roman', serif;
        }

        .subtitle {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 40px;
        }

        .org-name {
            font-size: 36px;
            color: #27ae60;
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .acronym {
            font-size: 20px;
            color: #34495e;
            margin-bottom: 30px;
        }

        .description {
            font-size: 18px;
            line-height: 1.6;
            color: #2c3e50;
            margin: 0 auto;
            max-width: 800px;
        }

        .ay-highlight {
            font-weight: bold;
            color: #27ae60;
        }

        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
        }

        .signature-block {
            display: inline-block;
            width: 250px;
            border-top: 2px solid #2c3e50;
            padding-top: 10px;
            margin: 0 50px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }

        .signature-title {
            font-size: 14px;
            color: #7f8c8d;
        }

        .date-issued {
            margin-top: 40px;
            font-size: 14px;
            color: #7f8c8d;
        }

        .background-seal {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            width: 400px;
            z-index: -1;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="inner-border">
            <div class="header">
                <img src="<?= base_url('SKSU_Official_Seal.png') ?>" class="seal" alt="Seal">
                <p class="university-name">Sultan Kudarat State University</p>
                <p class="office-name">University Student Government</p>
            </div>

            <h1 class="title">Certificate of Accreditation</h1>
            <p class="subtitle">This is to certify that</p>

            <h2 class="org-name"><?= esc($organization['name']) ?></h2>
            <p class="acronym">(<?= esc($organization['acronym']) ?>)</p>

            <p class="description">
                has successfully fulfilled all the requirements for accreditation as a recognized
                student organization for the Academic Year <span class="ay-highlight"><?= esc($academic_year) ?></span>,
                subject to the rules and regulations of the University and the University Student Government.
            </p>

            <div class="footer">
                <div class="signature-block">
                    <div class="signature-name">USG PRESIDENT</div>
                    <div class="signature-title">University Student Government</div>
                </div>
                <div class="signature-block">
                    <div class="signature-name">DIRECTOR OF STUDENT AFFAIRS</div>
                    <div class="signature-title">Office of Student Affairs</div>
                </div>
            </div>

            <p class="date-issued">Issued this <?= esc($date_issued) ?></p>
        </div>
    </div>
</body>

</html>