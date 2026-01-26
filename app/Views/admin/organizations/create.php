<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Organization - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 24px;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }

        .form-section {
            border-top: 2px solid #ecf0f1;
            padding-top: 20px;
            margin-top: 20px;
        }

        .form-section h3 {
            color: #3498db;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .error-list {
            list-style: none;
            padding: 0;
        }

        .error-list li {
            margin: 5px 0;
        }

        .back-link {
            color: #3498db;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .required {
            color: #e74c3c;
        }

        .help-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>USG Accreditation - Create Organization</h1>
    </nav>

    <div class="container">
        <a href="<?= base_url('admin/organizations') ?>" class="back-link">← Back to Organizations</a>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul class="error-list">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li>• <?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Create New Organization</h2>

            <form action="<?= base_url('admin/organizations/store') ?>" method="POST">
                <?= csrf_field() ?>
                <!-- Organization Information -->
                <div class="form-group">
                    <label>Organization Name <span class="required">*</span></label>
                    <input type="text" name="name" required value="<?= old('name') ?>"
                        placeholder="e.g., Computer Science Society">
                </div>

                <div class="form-group">
                    <label>Acronym</label>
                    <input type="text" name="acronym" value="<?= old('acronym') ?>" placeholder="e.g., CSS">
                    <div class="help-text">Short name or abbreviation (optional)</div>
                </div>

                <div class="form-group">
                    <label>Campus <span class="required">*</span></label>
                    <select name="campus" required>
                        <option value="">Select Campus</option>
                        <?php foreach ($campuses as $campus): ?>
                            <option value="<?= $campus ?>" <?= old('campus') == $campus ? 'selected' : '' ?>><?= $campus ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"
                        placeholder="Brief description of the organization"><?= old('description') ?></textarea>
                </div>

                <!-- Account Information -->
                <div class="form-section">
                    <h3>Account Information</h3>

                    <div class="form-group">
                        <label>Username <span class="required">*</span></label>
                        <input type="text" name="username" required value="<?= old('username') ?>"
                            placeholder="Login username">
                        <div class="help-text">Must be unique. This will be used to login.</div>
                    </div>

                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <input type="password" name="password" required placeholder="Create a strong password">
                        <div class="help-text">Minimum 6 characters</div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password <span class="required">*</span></label>
                        <input type="password" name="confirm_password" required placeholder="Re-enter password">
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Create Organization</button>
                    <a href="<?= base_url('admin/organizations') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function (e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    </script>
</body>

</html>