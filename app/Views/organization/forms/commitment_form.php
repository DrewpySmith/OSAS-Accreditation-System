<?= $this->extend('layouts/org_modern') ?>
<?php $title = 'Commitment Form Manager'; ?>

<?= $this->section('content') ?>

<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Action Bar -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print bg-card p-4 rounded-xl border shadow-sm">
        <div>
            <h2 class="text-xl font-bold tracking-tight">Commitment Form</h2>
            <p class="text-xs text-muted-foreground">Manage and print officer commitment documents.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= base_url('organization/commitment-form/print-list') ?>" target="_blank"
                class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print List
            </a>
            <?php if (isset($form['id'])): ?>
                <a href="<?= base_url('organization/commitment-form/print/' . $form['id']) ?>" target="_blank"
                    class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Print Single
                </a>
            <?php endif; ?>
            <button onclick="saveForm()"
                class="inline-flex items-center h-9 px-4 rounded-md bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Save Changes
            </button>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Sidebar: Officer List -->
        <div class="lg:col-span-4 no-print">
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden flex flex-col max-h-[800px]">
                <div class="p-4 border-b flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold">Existing Forms</h3>
                        <p class="text-[10px] text-muted-foreground uppercase tracking-widest mt-0.5">Drag to Reorder
                        </p>
                    </div>
                    <a href="<?= base_url('organization/commitment-form?new=1') ?>"
                        class="h-8 w-8 rounded-full bg-green-600/10 text-green-400 flex items-center justify-center hover:bg-green-600/20 transition-colors"
                        title="Create New">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                </div>

                <div class="p-2 space-y-1 overflow-y-auto flex-1" id="officerList">
                    <?php if (empty($forms)): ?>
                        <div class="p-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-8 h-8 mx-auto text-muted-foreground opacity-20 mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-xs text-muted-foreground">No forms found</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($forms as $f): ?>
                            <a href="<?= base_url('organization/commitment-form?id=' . $f['id']) ?>" data-id="<?= $f['id'] ?>"
                                class="group block p-3 rounded-lg border transition-all cursor-move
                                      <?= (isset($form['id']) && $form['id'] == $f['id'])
                                          ? 'border-green-500/30 bg-green-500/5'
                                          : 'border-white/5 bg-background hover:border-white/10' ?>">
                                <div class="flex items-center justify-between mb-1">
                                    <span
                                        class="text-xs font-bold truncate pr-2 <?= (isset($form['id']) && $form['id'] == $f['id']) ? 'text-green-300' : 'text-foreground' ?>">
                                        <?= esc($f['officer_name']) ?>
                                    </span>
                                    <span
                                        class="text-[9px] font-bold uppercase py-0.5 px-1.5 rounded bg-muted text-muted-foreground">
                                        <?= esc($f['status']) ?>
                                    </span>
                                </div>
                                <div class="text-[11px] text-muted-foreground truncate"><?= esc($f['position']) ?></div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Paper Form -->
        <div class="lg:col-span-8">
            <div
                class="bg-white text-black p-8 md:p-16 rounded-xl border-l-[6px] border-l-green-600 shadow-xl overflow-hidden min-h-[800px] relative font-serif">
                <!-- Watermark / Subtle Pattern -->
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296a3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                </div>

                <div class="text-center mb-12 space-y-1 relative z-10">
                    <p class="uppercase text-[10px] tracking-widest font-sans font-bold text-gray-500">Republic of the
                        Philippines</p>
                    <h3 class="text-lg font-black tracking-tight font-sans">SULTAN KUDARAT STATE UNIVERSITY</h3>
                    <p class="text-[10px] text-gray-500 font-sans">ACCESS, EJC Montilla, 9800 City of Tacurong</p>
                    <p class="text-[10px] text-gray-500 font-sans">Province of Sultan Kudarat</p>
                    <h2
                        class="text-3xl font-black mt-10 mb-6 tracking-[0.2em] font-sans border-y-2 border-black inline-block px-10 py-2">
                        COMMITMENT FORM</h2>
                </div>

                <div class="text-md leading-[2.5] text-justify space-y-8 relative z-10">
                    <p class="text-center">
                        I, <input type="text" id="officer_name"
                            class="border-b-2 border-gray-300 px-2 font-bold text-center focus:border-green-600 outline-none transition-colors w-[60%] sm:w-[50%]"
                            value="<?= isset($form['officer_name']) ? esc($form['officer_name']) : '' ?>"
                            placeholder="Full Name">,
                        hereby committed to take my responsibilities and duties as the newly elected
                        <input type="text" id="position"
                            class="border-b-2 border-gray-300 px-2 font-bold text-center focus:border-green-600 outline-none transition-colors w-[200px]"
                            value="<?= isset($form['position']) ? esc($form['position']) : '' ?>"
                            placeholder="Position">
                        of the <input type="text" id="organization_name"
                            class="border-b-2 border-gray-300 px-2 font-bold text-center bg-gray-50/50 w-[300px]"
                            value="<?= isset($organization['name']) ? esc($organization['name']) : (isset($form['organization_name']) ? esc($form['organization_name']) : '') ?>"
                            placeholder="Organization Name" readonly>
                        A.Y. <input type="text" id="academic_year"
                            class="border-b-2 border-gray-300 px-2 font-bold text-center focus:border-green-600 outline-none transition-colors w-[120px]"
                            value="<?= isset($form['academic_year']) ? esc($form['academic_year']) : esc($academic_year) ?>"
                            placeholder="2024-2025">.
                    </p>
                    <p class="indent-12">
                        I will render the best service I can give for the welfare of the said organization,
                        my fellow students, and University. I will respectfully abide the constitution and
                        By-Laws of the Republic of the Philippines and the rules and regulations of Sultan
                        Kudarat State University.
                    </p>
                    <p class="text-center font-black italic text-lg uppercase tracking-widest mt-12">
                        So help me God.
                    </p>

                    <div class="mt-16 text-center">
                        Signed this <input type="number" id="signed_day"
                            class="border-b-2 border-gray-300 px-1 font-bold text-center focus:border-green-600 outline-none transition-colors w-[40px]"
                            value="<?= isset($form['signed_date']) ? date('d', strtotime($form['signed_date'])) : date('d') ?>"
                            placeholder="DD"> day of
                        <select id="signed_month"
                            class="border-b-2 border-gray-300 px-1 font-bold bg-transparent focus:border-green-600 outline-none transition-colors w-[140px] cursor-pointer">
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

                    <div class="mt-20 flex flex-col items-center">
                        <div class="w-[300px] text-center">
                            <input type="text" id="signature_name"
                                class="w-full text-center font-black text-xl uppercase border-none pointer-events-none"
                                value="<?= isset($form['officer_name']) ? esc($form['officer_name']) : '' ?>" readonly>
                            <div
                                class="border-t-2 border-black mt-2 pt-1 font-sans font-bold text-[10px] uppercase tracking-tighter">
                                Signature over Printed Name
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="form_id" value="<?= isset($form['id']) ? $form['id'] : '' ?>">

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const csrfHeaderName = '<?= esc(config('Security')->headerName) ?>';
    let csrfToken = '<?= csrf_hash() ?>';

    function updateCsrfToken(next) {
        if (typeof next === 'string' && next.length > 0) csrfToken = next;
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
            .then(r => r.json())
            .then(data => {
                updateCsrfToken(data.csrf);
                if (data.success) {
                    alert('Form saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save form'));
                }
            });
    }

    document.getElementById('officer_name').addEventListener('input', function () {
        document.getElementById('signature_name').value = this.value;
    });

    // Sortable
    const el = document.getElementById('officerList');
    if (el) {
        Sortable.create(el, {
            animation: 150,
            ghostClass: 'bg-green-600/20',
            onEnd: function () {
                const order = Array.from(el.querySelectorAll('[data-id]')).map(item => item.dataset.id);
                fetch('/organization/commitment-form/reorder', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: csrfToken },
                    body: JSON.stringify({ order: order })
                })
                    .then(r => r.json())
                    .then(data => { if (data.csrf) updateCsrfToken(data.csrf); });
            }
        });
    }
</script>

<style>
    @media print {
        .bg-white {
            border-left: none !important;
            padding: 0 !important;
        }

        .text-md {
            font-size: 12pt !important;
            line-height: 1.6 !important;
        }

        input {
            border: none !important;
            border-bottom: 1pt solid black !important;
        }

        .indent-12 {
            text-indent: 1.5cm !important;
        }
    }
</style>

<?= $this->endSection() ?>