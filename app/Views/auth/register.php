<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Organization - USG Accreditation System</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            --panel-bg: rgba(30, 41, 59, 0.45);
            --panel-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-primary: #10b981; /* Green accents for Org Portal as per AGENTS.md */
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

        /* Ambient glow lights */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--accent-glow) 0%, rgba(16,185,129,0) 70%);
            top: -150px;
            left: -100px;
            z-index: 0;
            pointer-events: none;
        }

        .register-container {
            background: var(--panel-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--panel-border);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            width: 100%;
            max-width: 650px;
            z-index: 10;
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .header-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .header-section h1 {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .header-section p {
            color: var(--text-secondary);
            font-size: 14.5px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent-primary);
            margin: 28px 0 16px 0;
            border-bottom: 1px solid var(--panel-border);
            padding-bottom: 6px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        @media (max-width: 600px) {
            .form-group.full-width {
                grid-column: span 1;
            }
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 500;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--panel-border);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-primary);
            font-family: inherit;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
            background: rgba(15, 23, 42, 0.8);
        }

        .form-group textarea {
            resize: none;
            height: 90px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent-primary) 0%, #047857 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15.5px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            margin-top: 24px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 13.5px;
            line-height: 1.5;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .footer-links {
            text-align: center;
            margin-top: 24px;
            font-size: 13.5px;
        }

        .footer-links a {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--text-primary);
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="header-section">
            <h1>Register New Student Organization</h1>
            <p>Submit your application credentials to begin the accreditation process.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->get('errors')): ?>
            <div class="alert alert-error">
                <ul style="padding-left: 16px; margin: 0;">
                    <?php foreach (session()->get('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('register') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="section-title">1. Organization Details Title</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Organization Name</label>
                    <input type="text" id="name" name="name" required value="<?= old('name') ?>" placeholder="e.g. League of Computer Science Students">
                </div>
                <div class="form-group">
                    <label for="acronym">Acronym</label>
                    <input type="text" id="acronym" name="acronym" value="<?= old('acronym') ?>" placeholder="e.g. LiCSS">
                </div>
                <div class="form-group">
                    <label for="campus">Campus</label>
                    <select id="campus" name="campus" required>
                        <option value="">Select Campus</option>
                        <?php foreach ($campuses as $campus): ?>
                            <option value="<?= esc($campus) ?>" <?= old('campus') === $campus ? 'selected' : '' ?>><?= esc($campus) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="description">Organization Description</label>
                    <textarea id="description" name="description" placeholder="Briefly describe the purpose and goals of your organization..."><?= old('description') ?></textarea>
                </div>
            </div>

            <div class="section-title">2. Organization Officer Login</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="officer_email">Officer Email (Username)</label>
                    <input type="email" id="officer_email" name="officer_email" required value="<?= old('officer_email') ?>" placeholder="officer@email.com">
                </div>
                <div class="form-group">
                    <label for="officer_password">Account Password</label>
                    <input type="password" id="officer_password" name="officer_password" required placeholder="Choose a strong password (min 6 characters)">
                </div>
            </div>

            <div class="section-title">3. Organization Adviser Validation Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="adviser_name">Adviser Name</label>
                    <input type="text" id="adviser_name" name="adviser_name" required value="<?= old('adviser_name') ?>" placeholder="e.g. Dr. Juan Dela Cruz">
                </div>
                <div class="form-group">
                    <label for="adviser_email">Adviser Email Address</label>
                    <input type="email" id="adviser_email" name="adviser_email" required value="<?= old('adviser_email') ?>" placeholder="adviser@sksu.edu.ph">
                </div>
            </div>

            <button type="submit" class="btn-submit">Submit Registration Application</button>
        </form>

        <div class="footer-links">
            <p>Already have an account? <a href="<?= base_url('login') ?>">Login here</a></p>
        </div>
    </div>
</body>
</html>
