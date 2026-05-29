<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adviser Verification & Digital Signature - USG Accreditation System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            --panel-bg: rgba(30, 41, 59, 0.45);
            --panel-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-primary: #10b981;
            --accent-glow: rgba(16, 185, 129, 0.35);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-primary);
            overflow-y: auto;
            position: relative;
            padding: 40px 20px;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--accent-glow) 0%, rgba(16,185,129,0) 70%);
            top: -150px;
            left: -100px;
            pointer-events: none;
            z-index: 0;
        }

        .container {
            width: 100%;
            max-width: 750px;
            z-index: 10;
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* simulated paper document style */
        .paper-document {
            background: #ffffff;
            color: #1e293b;
            padding: 60px 50px;
            border-radius: 4px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            position: relative;
            border-top: 8px solid var(--accent-primary);
            margin-bottom: 24px;
        }

        @media (max-width: 600px) {
            .paper-document {
                padding: 30px 20px;
            }
        }

        .doc-header {
            text-align: center;
            border-bottom: 2px double #cbd5e1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .doc-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .doc-header p {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 6px;
        }

        .doc-body {
            font-size: 15px;
            line-height: 1.8;
            color: #334155;
            text-align: justify;
        }

        .doc-body p {
            margin-bottom: 20px;
        }

        .doc-details {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }

        .doc-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .doc-details td {
            padding: 6px 12px;
            font-size: 14px;
        }

        .doc-details td.label {
            font-weight: 600;
            color: #475569;
            width: 35%;
        }

        .doc-details td.val {
            color: #0f172a;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .signature-line-container {
            width: 280px;
            text-align: center;
        }

        .signature-box {
            width: 100%;
            height: 120px;
            border: 1px dashed #cbd5e1;
            background: #fafafa;
            border-radius: 4px;
            margin-bottom: 8px;
            position: relative;
            cursor: pointer;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-box .placeholder-text {
            color: #94a3b8;
            font-size: 12px;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .signature-box canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
        }

        .signature-line {
            border-top: 1px solid #475569;
            padding-top: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .signature-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-clear {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            border: 1px solid var(--panel-border);
        }

        .btn-clear:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent-primary) 0%, #047857 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            flex: 1;
            text-align: center;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
        }

        /* Success screen modal overlay */
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: all 0.4s ease;
            z-index: 100;
        }

        .success-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .success-card {
            background: #ffffff;
            color: #1e293b;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            transform: scale(0.9);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .success-overlay.active .success-card {
            transform: scale(1);
        }

        .success-icon {
            width: 72px;
            height: 72px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: var(--accent-primary);
        }

        .success-card h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #0f172a;
        }

        .success-card p {
            font-size: 14.5px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .btn-success-close {
            background: linear-gradient(135deg, var(--accent-primary) 0%, #047857 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Paper Document -->
        <div class="paper-document">
            <div class="doc-header">
                <h2>Formal Letter of Adviser Commitment</h2>
                <p>USG Accreditation System &bull; SKSU</p>
            </div>
            
            <div class="doc-body">
                <p><strong>TO THE OFFICE OF THE STUDENT AFFAIRS AND SERVICES:</strong></p>
                
                <p>
                    I, <strong>Prof./Dr. <?= esc($registration['adviser_name']) ?></strong>, hereby declare and formally confirm my agreement to serve as the official organization adviser for the applicant student organization listed below:
                </p>

                <div class="doc-details">
                    <table>
                        <tr>
                            <td class="label">Organization Name:</td>
                            <td class="val"><strong><?= esc($registration['name']) ?></strong></td>
                        </tr>
                        <?php if (!empty($registration['acronym'])): ?>
                        <tr>
                            <td class="label">Acronym:</td>
                            <td class="val"><?= esc($registration['acronym']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="label">Campus:</td>
                            <td class="val"><?= esc($registration['campus']) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Date Submitted:</td>
                            <td class="val"><?= date('F d, Y', strtotime($registration['created_at'])) ?></td>
                        </tr>
                    </table>
                </div>

                <p>
                    By providing my digital signature below, I certify that I have reviewed the registration credentials of this organization. I commit to provide appropriate mentorship, guide their officers, audit their financial operations, and review all documents submitted for USG accreditation for the active academic year.
                </p>
                
                <p>
                    Furthermore, I authorize the Sultan Kudarat State University Administration and USG officers to verify these credentials as part of their standard evaluation protocols.
                </p>
            </div>

            <!-- Interactive Signature Section -->
            <div class="signature-section">
                <div class="signature-line-container">
                    <div class="signature-box" id="sig-box">
                        <div class="placeholder-text" id="sig-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                            <span>Draw signature here</span>
                        </div>
                        <canvas id="sig-canvas"></canvas>
                    </div>
                    <div class="signature-line">
                        PROF. <?= strtoupper(esc($registration['adviser_name'])) ?>
                    </div>
                    <div class="signature-sub">
                        Official Academic Adviser
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="action-bar">
            <button class="btn btn-clear" id="btn-clear">Clear Signature</button>
            <button class="btn btn-submit" id="btn-submit">Confirm & Validate Application</button>
        </div>
    </div>

    <!-- Success Screen overlay -->
    <div class="success-overlay" id="success-overlay">
        <div class="success-card">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0110 21a3.745 3.745 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.745 3.745 0 013.296-1.043A3.745 3.745 0 0114 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                </svg>
            </div>
            <h3 id="success-title">Verification Successful</h3>
            <p id="success-text">The digital signature has been recorded. The organization request has been marked as validated and forwarded to the Administration office.</p>
            <a href="<?= base_url('login') ?>" class="btn-success-close">Go to Login</a>
        </div>
    </div>

    <!-- Canvas drawing scripts -->
    <script>
        const canvas = document.getElementById('sig-canvas');
        const ctx = canvas.getContext('2d');
        const placeholder = document.getElementById('sig-placeholder');
        const btnClear = document.getElementById('btn-clear');
        const btnSubmit = document.getElementById('btn-submit');
        const successOverlay = document.getElementById('success-overlay');

        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;
        let hasDrawn = false;

        // Resize canvas to match the styling size
        function resizeCanvas() {
            canvas.width = canvas.parentElement.clientWidth;
            canvas.height = canvas.parentElement.clientHeight;
            ctx.lineWidth = 2.5;
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#0f172a'; // dark navy ink signature
        }

        window.addEventListener('load', resizeCanvas);
        window.addEventListener('resize', resizeCanvas);

        // Drawing events
        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            if (e.touches && e.touches.length > 0) {
                return {
                    x: e.touches[0].clientX - rect.left,
                    y: e.touches[0].clientY - rect.top
                };
            }
            return {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        }

        function startDrawing(e) {
            e.preventDefault();
            isDrawing = true;
            const pos = getPos(e);
            lastX = pos.x;
            lastY = pos.y;
            placeholder.style.display = 'none';
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getPos(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            
            lastX = pos.x;
            lastY = pos.y;
            hasDrawn = true;
        }

        function stopDrawing() {
            isDrawing = false;
        }

        // Mouse listeners
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stopDrawing);

        // Touch listeners
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        window.addEventListener('touchend', stopDrawing);

        // Clear button
        btnClear.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            placeholder.style.display = 'flex';
            hasDrawn = false;
        });

        // Submit button with AJAX
        btnSubmit.addEventListener('click', () => {
            if (!hasDrawn) {
                alert('Please draw your digital signature before validating.');
                return;
            }

            // Convert canvas drawing to base64 string
            const signatureDataUrl = canvas.toDataURL();
            
            const formData = new FormData();
            formData.append('signature', signatureDataUrl);
            
            // Add CSRF token
            const csrfHeaderName = 'X-CSRF-TOKEN';
            const csrfHash = '<?= csrf_hash() ?>';
            formData.append('<?= csrf_token() ?>', csrfHash);

            btnSubmit.disabled = true;
            btnSubmit.innerText = 'Verifying Signature...';

            fetch('<?= base_url('validate-org/sign/' . $registration['adviser_token']) ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    successOverlay.classList.add('active');
                } else {
                    alert(data.message);
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = 'Confirm & Validate Application';
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while validating. Please check SMTP/network credentials.');
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'Confirm & Validate Application';
            });
        });
    </script>
</body>
</html>
