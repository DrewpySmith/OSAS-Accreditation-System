<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commitment Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f0f2f5;
            color: #1a202c;
        }

        .navbar {
            background: #2f855a;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-title {
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-save {
            background: #3182ce;
            color: white;
        }

        .btn-save:hover {
            background: #2b6cb0;
        }

        .btn-print {
            background: #2f855a;
            color: white;
        }

        .btn-print:hover {
            background: #276749;
        }

        .btn-list {
            background: #805ad5;
            color: white;
        }

        .btn-list:hover {
            background: #6b46c1;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .sidebar-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 900px) {
            .sidebar-layout {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .form-item {
            display: block;
            padding: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .form-item:hover {
            transform: translateX(4px);
            border-color: #3182ce;
        }

        .form-item.active {
            background: #ebf8ff;
            border-color: #3182ce;
            border-left-color: #3182ce;
        }

        .form-item-name {
            font-weight: 700;
            font-size: 0.9375rem;
            margin-bottom: 0.25rem;
        }

        .form-item-pos {
            font-size: 0.8125rem;
            color: #718096;
        }

        .form-status {
            display: inline-block;
            font-size: 0.75rem;
            padding: 0.125rem 0.5rem;
            border-radius: 1rem;
            background: #e2e8f0;
            margin-top: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .form-paper {
            background: white;
            padding: 4rem;
            position: relative;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
        }

        .paper-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .paper-content {
            font-size: 1.1rem;
            line-height: 2;
            text-align: justify;
            color: #1a202c;
        }

        .underline-input {
            border: none;
            border-bottom: 2px solid #cbd5e0;
            padding: 0 0.5rem;
            font-family: inherit;
            font-size: inherit;
            font-weight: 700;
            color: #2d3748;
            outline: none;
            transition: border-color 0.2s;
            background: transparent;
        }

        .underline-input:focus {
            border-color: #3182ce;
        }

        .signature-section {
            margin-top: 4rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sig-block {
            text-align: center;
            width: 300px;
        }

        .sig-label {
            border-top: 2px solid #2d3748;
            margin-top: 1rem;
            padding-top: 0.5rem;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .container {
                margin: 0;
                max-width: none;
                padding: 0;
            }

            .form-paper {
                border: none;
                box-shadow: none;
                padding: 0;
            }

            body {
                background: white;
            }
        }

        .form-item {
            cursor: grab;
        }

        .form-item:active {
            cursor: grabbing;
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #ebf8ff;
            border: 2px dashed #3182ce;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>

<body>
    <div class="navbar no-print">
        <div class="navbar-title">Commitment Form Manager</div>
        <div class="navbar-buttons">
            <a href="<?= base_url('organization/dashboard') ?>" class="btn btn-back">🏠 Dashboard</a>
            <a href="<?= base_url('organization/commitment-form/print-list') ?>" target="_blank"
                class="btn btn-list">🖨️ Print Officer List</a>
            <button onclick="saveForm()" class="btn btn-save">💾 Save Form</button>
            <?php if (isset($form['id'])): ?>
                <a href="<?= base_url('organization/commitment-form/print/' . $form['id']) ?>" target="_blank"
                    class="btn btn-print">📄 Print PDF</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="sidebar-layout">
            <div class="no-print">
                <div class="card">
                    <div class="card-title" style="justify-content: space-between;">
                        <span>📋 Existing Forms</span>
                        <span style="font-size: 0.75rem; color: #a0aec0; font-weight: normal;">Drag to reorder</span>
                    </div>
                    <?php if (empty($forms)): ?>
                        <div style="text-align: center; padding: 2rem; color: #a0aec0; font-size: 0.875rem;">
                            No forms created for A.Y. <?= esc($academic_year) ?>
                        </div>
                    <?php else: ?>
                        <div class="form-list" id="officerList">
                            <?php foreach ($forms as $f): ?>
                                <a href="<?= base_url('organization/commitment-form?id=' . $f['id']) ?>"
                                    class="form-item <?= (isset($form['id']) && $form['id'] == $f['id']) ? 'active' : '' ?>"
                                    data-id="<?= $f['id'] ?>">
                                    <div class="form-item-name"><?= esc($f['officer_name']) ?></div>
                                    <div class="form-item-pos"><?= esc($f['position']) ?></div>
                                    <div class="form-status"><?= esc($f['status']) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <a href="<?= base_url('organization/commitment-form?new=1') ?>"
                        style="display: block; margin-top: 1rem; text-align: center; text-decoration: none; color: #3182ce; font-weight: 600; font-size: 0.875rem;">
                        + Create New Form
                    </a>
                </div>
            </div>

            <div class="form-paper">
                <div class="paper-header">
                    <p style="margin: 0; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Republic
                        of the Philippines</p>
                    <h3 style="margin: 0.5rem 0; font-weight: 800; font-size: 1.25rem;">SULTAN KUDARAT STATE UNIVERSITY
                    </h3>
                    <p style="margin: 0; font-size: 0.8rem; color: #4a5568;">ACCESS, EJC Montilla, 9800 City of Tacurong
                    </p>
                    <p style="margin: 0; font-size: 0.8rem; color: #4a5568;">Province of Sultan Kudarat</p>
                    <h2 style="margin: 2.5rem 0; font-weight: 900; letter-spacing: 0.1em; color: #2d3748;">COMMITMENT
                        FORM</h2>
                </div>

                <div class="paper-content">
                    <p style="text-align: center;">
                        I, <input type="text" id="officer_name" class="underline-input" style="width: 320px;"
                            value="<?= isset($form['officer_name']) ? esc($form['officer_name']) : '' ?>"
                            placeholder="Full Name">,
                        hereby committed to take my responsibilities and duties as the newly elected
                        <input type="text" id="position" class="underline-input" style="width: 200px;"
                            value="<?= isset($form['position']) ? esc($form['position']) : '' ?>"
                            placeholder="Position">
                        of the <input type="text" id="organization_name" class="underline-input"
                            style="width: 300px; background: #f7fafc;"
                            value="<?= isset($organization['name']) ? esc($organization['name']) : (isset($form['organization_name']) ? esc($form['organization_name']) : '') ?>"
                            placeholder="Organization Name" readonly>
                        A.Y. <input type="text" id="academic_year" class="underline-input" style="width: 120px;"
                            value="<?= isset($form['academic_year']) ? esc($form['academic_year']) : esc($academic_year) ?>"
                            placeholder="2024-2025">.
                    </p>
                    <p style="margin-top: 1.5rem; text-indent: 2rem;">
                        I will render the best service I can give for the welfare of the said organization,
                        my fellow students, and University. I will respectfully abide the constitution and
                        By-Laws of the Republic of the Philippines and the rules and regulations of Sultan
                        Kudarat State University.
                    </p>
                    <p style="margin-top: 1.5rem; text-align: center; font-weight: 700;">
                        So help me God.
                    </p>

                    <div style="margin-top: 3rem; text-align: center;">
                        Signed this <input type="number" id="signed_day" class="underline-input" style="width: 60px;"
                            value="<?= isset($form['signed_date']) ? date('d', strtotime($form['signed_date'])) : date('d') ?>"
                            placeholder="Day"> day of
                        <select id="signed_month" class="underline-input" style="width: 140px; cursor: pointer;">
                            <?php
                            $months = [
                                "01" => "January",
                                "02" => "February",
                                "03" => "March",
                                "04" => "April",
                                "05" => "May",
                                "06" => "June",
                                "07" => "July",
                                "08" => "August",
                                "09" => "September",
                                "10" => "October",
                                "11" => "November",
                                "12" => "December"
                            ];
                            $currentMonth = isset($form['signed_date']) ? date('m', strtotime($form['signed_date'])) : date('m');
                            foreach ($months as $val => $name): ?>
                                <option value="<?= $val ?>" <?= $currentMonth == $val ? 'selected' : '' ?>><?= $name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>.
                    </div>

                    <div class="signature-section">
                        <div class="sig-block">
                            <input type="text" id="signature_name"
                                style="border: none; text-align: center; font-weight: 800; font-size: 1.1rem; width: 100%; text-transform: uppercase;"
                                value="<?= isset($form['officer_name']) ? esc($form['officer_name']) : '' ?>"
                                placeholder="Your Full Name" readonly>
                            <div class="sig-label">Signature over Printed Name</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="form_id" value="<?= isset($form['id']) ? $form['id'] : '' ?>">

    <script>
        const csrfHeaderName = '<?= esc(config('Security')->headerName) ?>';
        let csrfToken = '<?= csrf_hash() ?>';

        function updateCsrfToken(next) {
            if (typeof next === 'string' && next.length > 0) {
                csrfToken = next;
            }
        }

        function saveForm() {
            const day = document.getElementById('signed_day').value;
            const month = document.getElementById('signed_month').value;
            const year = new Date().getFullYear();
            const signedDate = `${year}-${month}-${day.padStart(2, '0')}`;

            const formData = {
                officer_name: document.getElementById('officer_name').value,
                position: document.getElementById('position').value,
                organization_name: document.getElementById('organization_name').value,
                academic_year: document.getElementById('academic_year').value,
                signed_date: signedDate
            };

            const formId = document.getElementById('form_id').value;
            const url = formId ? `/organization/commitment-form/update/${formId}` : '/organization/commitment-form/store';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeaderName]: csrfToken
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    updateCsrfToken(data.csrf);
                    if (data.success) {
                        alert('Form saved and submitted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to save form'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the form');
                });
        }

        document.getElementById('officer_name').addEventListener('input', function () {
            document.getElementById('signature_name').value = this.value;
        });

        // Initialize Sortable
        const el = document.getElementById('officerList');
        if (el) {
            Sortable.create(el, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    const order = Array.from(el.querySelectorAll('.form-item')).map(item => item.dataset.id);
                    saveOrder(order);
                }
            });
        }

        function saveOrder(order) {
            fetch('/organization/commitment-form/reorder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeaderName]: csrfToken
                },
                body: JSON.stringify({ order: order })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.csrf) updateCsrfToken(data.csrf);
                    if (!data.success) alert('Failed to save order: ' + data.message);
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>

</html>