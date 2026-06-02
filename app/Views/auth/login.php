<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSAS Accreditation System - Login</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            --panel-bg: rgba(30, 41, 59, 0.45);
            --panel-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-primary: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.45);
            --accent-hover: #2563eb;
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
            overflow-x: hidden;
            position: relative;
        }

        /* Decorative glowing ambient spots */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--accent-glow) 0%, rgba(59,130,246,0) 70%);
            top: -100px;
            left: -100px;
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.15) 0%, rgba(16,185,129,0) 70%);
            bottom: -150px;
            right: -100px;
            z-index: 0;
            pointer-events: none;
        }

        .login-container {
            background: var(--panel-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--panel-border);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            width: 100%;
            max-width: 420px;
            z-index: 10;
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-section .seal {
            width: 96px;
            height: 96px;
            object-fit: contain;
            display: block;
            margin: 0 auto 16px auto;
            filter: drop-shadow(0 0 12px rgba(59, 130, 246, 0.3));
            transition: transform 0.4s ease;
        }

        .logo-section .seal:hover {
            transform: rotate(5deg) scale(1.05);
        }

        .logo-section h1 {
            color: var(--text-primary);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .logo-section p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .form-group label {
            display: block;
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--panel-border);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-primary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            background: rgba(15, 23, 42, 0.8);
        }

        .forgot-password-link {
            font-size: 12px;
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-password-link:hover {
            color: var(--text-primary);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent-primary) 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 13.5px;
            line-height: 1.5;
            animation: slideDown 0.3s ease;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .auth-links {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
        }

        .auth-links a {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-links a:hover {
            color: var(--text-primary);
            text-decoration: underline;
        }

        .footer-text {
            text-align: center;
            margin-top: 32px;
            color: var(--text-secondary);
            font-size: 12px;
            line-height: 1.6;
        }

        /* Ambient Premium Modal Styling */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .modal.active {
            opacity: 1;
            pointer-events: all;
        }

        .modal-content {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            backdrop-filter: blur(20px);
            padding: 32px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            transform: scale(0.95);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal.active .modal-content {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: var(--text-primary);
        }

        .modal p {
            font-size: 13.5px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <img class="seal" src="<?= base_url('SKSU_Official_Seal.png') ?>" alt="SKSU Official Seal">
            <h1>SKSU OSAS</h1>
            <p>Accreditation Portal</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('authenticate') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Username / Email</label>
                <input type="text" id="username" name="username" required autofocus placeholder="Enter your username or email">
            </div>

            <div class="form-group">
                <div class="form-group-label-row">
                    <label for="password">Password</label>
                    <a href="#" class="forgot-password-link" id="forgot-password-trigger">Forgot password?</a>
                </div>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="auth-links">
            <p>New organization? <a href="<?= base_url('register') ?>">Register now</a></p>
        </div>

        <div class="footer-text">
            <p>Sultan Kudarat State University</p>
            <p>&copy; <?= date('Y') ?> OSAS. All Rights Reserved.</p>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal" id="forgot-password-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reset Password</h3>
                <button class="modal-close" id="forgot-password-close">&times;</button>
            </div>
            <p>Enter your registered account username/email. We will verify your account and email you a secure, timed reset link.</p>
            
            <form action="<?= base_url('forgot-password') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="recovery-email">Username / Email</label>
                    <input type="text" id="recovery-email" name="email" required placeholder="Enter username or email address">
                </div>
                <button type="submit" class="btn-login" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">Send Verification Link</button>
            </form>
        </div>
    </div>

    <script>
        const trigger = document.getElementById('forgot-password-trigger');
        const modal = document.getElementById('forgot-password-modal');
        const close = document.getElementById('forgot-password-close');

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            modal.classList.add('active');
        });

        close.addEventListener('click', () => {
            modal.classList.remove('active');
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    </script>
</body>
</html>