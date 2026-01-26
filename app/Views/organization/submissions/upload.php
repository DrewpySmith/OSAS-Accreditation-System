<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .required {
            color: #dc3545;
        }

        .help-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-label {
            display: block;
            padding: 40px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-input-label:hover {
            border-color: #007bff;
            background: #e7f3ff;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2>Upload Document</h2>
            <p style="color: #7f8c8d; margin-bottom: 30px;">Submit your documents for admin review</p>

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

            <form action="<?= base_url('organization/submissions/upload') ?>" method="POST"
                enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Document Type <span class="required">*</span></label>
                    <select name="document_type" required>
                        <option value="">-- Select Document Type --</option>
                        <?php foreach (($document_types ?? []) as $val => $label): ?>
                            <option value="<?= esc($val) ?>"><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Document Title <span class="required">*</span></label>
                    <input type="text" name="document_title" required placeholder="e.g., Financial Report Q3 2024"
                        value="<?= old('document_title') ?>">
                    <div class="help-text">Give your document a descriptive title</div>
                </div>

                <div class="form-group">
                    <label>Academic Year <span class="required">*</span></label>
                    <input type="text" name="academic_year" required placeholder="e.g., 2024-2025"
                        value="<?= old('academic_year') ?>">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"
                        placeholder="Add any notes or comments about this document"><?= old('description') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Upload File <span class="required">*</span></label>
                    <div class="file-input-wrapper">
                        <div class="file-input-label" id="file-label">
                            <p style="font-size: 48px; margin: 0;">📄</p>
                            <p style="margin: 10px 0;"><strong>Click to browse</strong> or drag and drop</p>
                            <p style="color: #7f8c8d; font-size: 14px;">Supported formats: PDF, DOC, DOCX (Max 10MB)</p>
                            <p id="file-name" style="color: #007bff; font-weight: 600; margin-top: 10px;"></p>
                        </div>
                        <input type="file" name="document_file" id="document_file" required accept=".pdf,.doc,.docx">
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">📤 Upload Document</button>
                    <a href="<?= base_url('organization/submissions') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div
            style="background: white; padding: 20px; border-radius: 10px; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3>📝 Submission Guidelines:</h3>
            <ul style="color: #7f8c8d;">
                <li>Ensure your document is properly formatted and complete</li>
                <li>Files must be in PDF, DOC, or DOCX format</li>
                <li>Maximum file size is 10MB</li>
                <li>Use clear, descriptive titles for easy identification</li>
                <li>Include the academic year in your submission</li>
                <li>You can upload multiple documents of the same type</li>
            </ul>
        </div>
    </div>

    <script>
        document.getElementById('document_file').addEventListener('change', function (e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                document.getElementById('file-name').textContent = 'Selected: ' + fileName;
            }
        });
    </script>
</body>

</html>