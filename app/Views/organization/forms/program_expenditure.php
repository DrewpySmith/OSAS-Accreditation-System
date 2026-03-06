<?= $this->extend('layouts/org_modern') ?>
<?php $title = 'Program of Expenditures'; ?>

<?= $this->section('content') ?>

<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Page Header -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print bg-card p-4 rounded-xl border shadow-sm">
        <div>
            <h2 class="text-xl font-bold tracking-tight">Program of Expenditures</h2>
            <p class="text-xs text-muted-foreground">Detailed summary of expected collections and budgeting for A.Y.
                <?= esc($academic_year) ?>.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="addRow()"
                class="inline-flex items-center h-9 px-4 rounded-md bg-green-600 text-white text-xs font-bold hover:bg-green-700 transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Member Entry
            </button>
            <button onclick="downloadPDF()"
                class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                PDF
            </button>
            <button onclick="window.print()"
                class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Content Card -->
    <div
        class="bg-card rounded-xl border shadow-sm overflow-hidden flex flex-col print:bg-white print:text-black print:border-none print:shadow-none">
        <!-- Official Header -->
        <div class="p-8 md:p-12 text-center border-b print:p-0 print:border-none">
            <div class="space-y-1 mb-6">
                <p class="uppercase text-[10px] tracking-widest font-bold text-muted-foreground print:text-gray-600">
                    Republic of the Philippines</p>
                <h3 class="text-lg font-black tracking-tight print:text-black">SULTAN KUDARAT STATE UNIVERSITY</h3>
                <p class="text-[10px] text-muted-foreground print:text-gray-600 font-medium">ACCESS, EJC Montilla, 9800
                    City of Tacurong</p>
                <p class="text-[10px] text-muted-foreground print:text-gray-600 font-medium">Province of Sultan Kudarat
                </p>
                <h2
                    class="text-3xl font-black mt-10 mb-6 tracking-[0.1em] border-y py-2 border-green-500/20 text-green-400 print:text-black print:border-black uppercase">
                    Program of Expenditures</h2>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-sm font-bold uppercase text-muted-foreground print:text-gray-600">A.Y.</span>
                    <input type="text" id="academic_year"
                        class="bg-transparent border-b-2 border-white/10 font-black text-center focus:border-green-500 outline-none w-[120px] print:border-black print:text-black"
                        value="<?= esc($academic_year) ?>" placeholder="2024-2025">
                </div>
                <div class="mt-4 pt-4 border-t border-white/5 print:border-black">
                    <p
                        class="text-xl font-black underline decoration-green-500/50 underline-offset-4 print:text-black print:decoration-black">
                        <?= isset($organization['name']) ? esc($organization['name']) : '' ?>
                    </p>
                    <p class="text-[10px] uppercase font-bold text-muted-foreground mt-1 print:text-gray-600">
                        (Organization Name)</p>
                </div>
            </div>
        </div>

        <!-- Expected Collections Table -->
        <div class="p-4 md:p-8 space-y-4">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-green-400 mb-4 print:text-black">Expected
                Collections</h3>
            <div class="rounded-lg border overflow-hidden print:border-black">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 print:bg-gray-100">
                        <tr>
                            <th class="h-10 px-4 text-left font-bold text-muted-foreground print:text-black">Type of Fee
                            </th>
                            <th class="h-10 px-4 text-right font-bold text-muted-foreground print:text-black w-[150px]">
                                Amount</th>
                            <th class="h-10 px-4 text-left font-bold text-muted-foreground print:text-black w-[180px]">
                                Frequency</th>
                            <th
                                class="h-10 px-4 text-center font-bold text-muted-foreground print:text-black w-[150px]">
                                Students</th>
                            <th class="h-10 px-4 text-right font-bold text-muted-foreground print:text-black w-[180px]">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody id="expenditure-body" class="divide-y print:divide-black">
                        <?php if (empty($expenditures)): ?>
                            <tr>
                                <td colspan="5" class="p-12 text-center text-muted-foreground italic">
                                    No expenditure entries yet. Click "Add Member Entry" to start.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenditures as $exp): ?>
                                <tr class="group hover:bg-white/5 transition-colors print:hover:bg-transparent">
                                    <td class="p-4 font-bold print:text-black"><?= esc($exp['fee_type']) ?></td>
                                    <td class="p-4 text-right font-medium print:text-black">₱
                                        <?= number_format($exp['amount'], 2) ?></td>
                                    <td class="p-4 text-muted-foreground print:text-black"><?= esc($exp['frequency']) ?></td>
                                    <td class="p-4 text-center text-muted-foreground print:text-black">
                                        <?= number_format($exp['number_of_students']) ?></td>
                                    <td class="p-4 text-right font-black text-green-400 print:text-black">₱
                                        <?= number_format($exp['total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-muted/30 font-black print:bg-gray-50">
                        <tr>
                            <td colspan="4" class="p-4 text-right uppercase tracking-widest text-xs print:text-black">
                                Grand Total</td>
                            <td class="p-4 text-right text-xl text-green-400 print:text-black">₱
                                <?= number_format($grand_total ?? 0, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Signatures Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-20 mt-20 pb-8 print:text-black">
                <div class="text-center">
                    <p class="text-[10px] font-bold uppercase text-muted-foreground mb-4 print:text-gray-600">Prepared
                        by:</p>
                    <input type="text" id="head_name"
                        class="w-full text-center font-black text-lg bg-transparent border-b-2 border-white/10 focus:border-green-500 outline-none print:border-black print:text-black"
                        placeholder="Head of Organization"
                        value="<?= isset($signatory['head_name']) ? esc($signatory['head_name']) : '' ?>">
                    <p
                        class="text-[10px] font-bold uppercase text-muted-foreground mt-2 print:text-black tracking-widest leading-none">
                        Head of Organization</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] font-bold uppercase text-muted-foreground mb-4 print:text-gray-600">Approved:
                    </p>
                    <input type="text" id="adviser_name"
                        class="w-full text-center font-black text-lg bg-transparent border-b-2 border-white/10 focus:border-green-500 outline-none print:border-black print:text-black"
                        placeholder="Adviser Name"
                        value="<?= isset($signatory['adviser_name']) ? esc($signatory['adviser_name']) : '' ?>">
                    <p
                        class="text-[10px] font-bold uppercase text-muted-foreground mt-2 print:text-black tracking-widest leading-none">
                        Adviser</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modernized Modal -->
<div id="rowModal"
    class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-in fade-in duration-200">
    <div
        class="bg-card w-full max-w-lg rounded-2xl border shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="p-6 border-b flex items-center justify-between bg-muted/30">
            <h3 class="text-lg font-bold tracking-tight">Add Expenditure Entry</h3>
            <button onclick="closeModal()" class="text-muted-foreground hover:text-foreground p-1 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="expenditureForm" class="p-6 space-y-4">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Type of
                    Fee</label>
                <input type="text" id="fee_type" required placeholder="e.g. Membership Fee"
                    class="w-full bg-background border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Amount (Per
                        Student)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-muted-foreground text-xs font-bold">₱</span>
                        <input type="number" id="amount" step="0.01" required placeholder="0.00"
                            class="w-full bg-background border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">No. of
                        Students</label>
                    <input type="number" id="number_of_students" required placeholder="0"
                        class="w-full bg-background border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all">
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Frequency of
                    Collection</label>
                <input type="text" id="frequency" required placeholder="e.g. Once / Per Semester"
                    class="w-full bg-background border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all">
            </div>

            <div class="p-4 rounded-xl bg-green-500/5 border border-green-500/10 flex justify-between items-center">
                <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Estimated Total</span>
                <span id="total_preview" class="text-xl font-black text-green-400">₱ 0.00</span>
            </div>

            <div class="pt-4 flex gap-2 justify-end">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 rounded-lg border text-sm font-bold hover:bg-muted transition-all">Cancel</button>
                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-green-600 text-white text-sm font-black hover:bg-green-700 transition-all shadow-lg shadow-green-600/20">
                    Save Entry
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const csrfHeaderName = '<?= esc(config('Security')->headerName) ?>';
    let csrfToken = '<?= csrf_hash() ?>';

    function updateCsrfToken(next) {
        if (typeof next === 'string' && next.length > 0) csrfToken = next;
    }

    function addRow() {
        document.getElementById('expenditureForm').reset();
        document.getElementById('total_preview').textContent = '₱ 0.00';
        document.getElementById('rowModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('rowModal').classList.add('hidden');
    }

    function updateTotal() {
        const amt = parseFloat(document.getElementById('amount').value) || 0;
        const stu = parseInt(document.getElementById('number_of_students').value) || 0;
        document.getElementById('total_preview').textContent = '₱ ' + (amt * stu).toLocaleString('en-US', { minimumFractionDigits: 2 });
    }

    document.getElementById('amount').addEventListener('input', updateTotal);
    document.getElementById('number_of_students').addEventListener('input', updateTotal);

    document.getElementById('expenditureForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData();
        fd.append('academic_year', document.getElementById('academic_year').value);
        fd.append('fee_type', document.getElementById('fee_type').value);
        fd.append('amount', document.getElementById('amount').value);
        fd.append('frequency', document.getElementById('frequency').value);
        fd.append('number_of_students', document.getElementById('number_of_students').value);
        fd.append('head_name', document.getElementById('head_name').value);
        fd.append('adviser_name', document.getElementById('adviser_name').value);

        fetch('/organization/program-expenditure/store', {
            method: 'POST',
            headers: { [csrfHeaderName]: csrfToken },
            body: fd
        })
            .then(r => r.json())
            .then(data => {
                updateCsrfToken(data.csrf);
                if (data.success) { location.reload(); }
                else { alert('Error: ' + data.message); }
            });
    });

    function downloadPDF() {
        const ay = document.getElementById('academic_year').value;
        if (!ay) { alert('Enter A.Y.'); return; }
        window.location.href = `/organization/program-expenditure/download/${ay}`;
    }
</script>

<?= $this->endSection() ?>