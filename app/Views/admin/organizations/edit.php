<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Organization - Admin</title>
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

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-logout {
            padding: 8px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
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

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 720px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>USG Accreditation - Admin Panel</h1>
        <div class="navbar-right">
            <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <a href="<?= base_url('admin/organizations') ?>">Organizations</a>
            <a href="<?= base_url('admin/documents') ?>">Documents</a>
            <a href="<?= base_url('admin/statistics') ?>">Statistics</a>
            <a href="<?= base_url('logout') ?>" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <a href="<?= base_url('admin/organizations') ?>" class="back-link">← Back to Organizations</a>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="margin-bottom: 20px; color: #2c3e50;">Edit Organization</h2>

            <form action="<?= base_url('admin/organizations/update/' . $organization['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="grid">
                    <div class="form-group">
                        <label for="name">Organization Name</label>
                        <input type="text" id="name" name="name" value="<?= esc($organization['name'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="acronym">Acronym</label>
                        <input type="text" id="acronym" name="acronym"
                            value="<?= esc($organization['acronym'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="campus">Campus</label>
                        <select id="campus" name="campus" required>
                            <option value="">Select Campus</option>
                            <?php foreach ($campuses as $campus): ?>
                                <option value="<?= $campus ?>" <?= (isset($organization['campus']) && $organization['campus'] == $campus) ? 'selected' : '' ?>><?= $campus ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description"
                        name="description"><?= esc($organization['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <?php $st = $organization['status'] ?? 'active'; ?>
                        <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="suspended" <?= $st === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="new_password">Reset Organization Account Password (optional)</label>
                    <input type="password" id="new_password" name="new_password"
                        placeholder="Leave blank to keep current password">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?= base_url('admin/organizations') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>