<?= $this->extend('layouts/org_modern') ?>
<?php $title = 'Financial Report'; ?>

<?= $this->section('content') ?>

<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Action Bar -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print bg-card p-4 rounded-xl border shadow-sm">
        <div>
            <h2 class="text-xl font-bold tracking-tight">Financial Report</h2>
            <p class="text-xs text-muted-foreground">Comprehensive summary of collections and expenses for A.Y.
                <?= esc($report['academic_year'] ?? 'N/A') ?>.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="downloadPDF()"
                class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
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
            <button onclick="saveForm()"
                class="inline-flex items-center h-9 px-4 rounded-md bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Save Report
            </button>
        </div>
    </div>

    <!-- Main Report Content -->
    <div
        class="bg-card rounded-xl border shadow-sm overflow-hidden flex flex-col print:bg-white print:text-black print:border-none print:shadow-none">
        <!-- Header Section -->
        <div class="p-8 md:p-12 text-center border-b print:p-0 print:border-none">
            <div class="space-y-1 mb-8">
                <p class="uppercase text-[10px] tracking-widest font-bold text-muted-foreground print:text-gray-600">
                    Republic of the Philippines</p>
                <h3 class="text-lg font-black tracking-tight print:text-black">SULTAN KUDARAT STATE UNIVERSITY</h3>
                <p class="text-[10px] text-muted-foreground print:text-gray-600 font-medium">ACCESS, EJC Montilla, 9800
                    City of Tacurong</p>
                <p class="text-[10px] text-muted-foreground print:text-gray-600 font-medium">Province of Sultan Kudarat
                </p>
                <h2
                    class="text-3xl font-black mt-10 mb-6 tracking-[0.1em] border-y py-2 border-green-500/20 text-green-400 print:text-black print:border-black">
                    FINANCIAL REPORTS</h2>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-sm font-bold uppercase text-muted-foreground print:text-gray-600">A.Y.</span>
                    <input type="text" id="academic_year"
                        class="bg-transparent border-b-2 border-white/10 font-black text-center focus:border-green-500 outline-none w-[120px] print:border-black"
                        value="<?= isset($report['academic_year']) ? $report['academic_year'] : '' ?>"
                        placeholder="2024-2025">
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

        <div class="p-8 md:p-12 space-y-12 relative">
            <!-- Summary of Collections -->
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black uppercase tracking-widest text-green-400 print:text-black">1. Summary
                        of Collection</h3>
                    <button
                        class="no-print h-8 px-3 rounded-md bg-white/5 hover:bg-white/10 text-[10px] font-bold transition-colors flex items-center gap-1.5"
                        onclick="addCollectionRow()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Row
                    </button>
                </div>
                <div class="rounded-lg border overflow-hidden print:border-black">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 print:bg-gray-100">
                            <tr>
                                <th class="h-10 px-4 text-left font-bold text-muted-foreground print:text-black">Type of
                                    Collection</th>
                                <th
                                    class="h-10 px-4 text-right font-bold text-muted-foreground print:text-black w-[200px]">
                                    Amount</th>
                                <th class="no-print h-10 px-4 text-center font-bold text-muted-foreground w-[80px]">
                                </th>
                            </tr>
                        </thead>
                        <tbody id="collections-body" class="divide-y print:divide-black">
                            <!-- Collections added via JS -->
                        </tbody>
                        <tfoot class="bg-muted/30 font-black print:bg-gray-50">
                            <tr>
                                <td class="p-4 text-right print:text-black">Grand Total</td>
                                <td class="p-4 text-right text-green-400 print:text-black" id="grand-total-collection">₱
                                    0.00</td>
                                <td class="no-print"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <!-- Summary of Expenses -->
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black uppercase tracking-widest text-green-400 print:text-black">2. Summary
                        of Expenses</h3>
                    <button
                        class="no-print h-8 px-3 rounded-md bg-white/5 hover:bg-white/10 text-[10px] font-bold transition-colors flex items-center gap-1.5"
                        onclick="addExpenseRow()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Row
                    </button>
                </div>
                <div class="rounded-lg border overflow-hidden print:border-black">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 print:bg-gray-100">
                            <tr>
                                <th class="h-10 px-4 text-left font-bold text-muted-foreground print:text-black">
                                    Activity / Project</th>
                                <th
                                    class="h-10 px-4 text-right font-bold text-muted-foreground print:text-black w-[200px]">
                                    Amount</th>
                                <th class="no-print h-10 px-4 text-center font-bold text-muted-foreground w-[80px]">
                                </th>
                            </tr>
                        </thead>
                        <tbody id="expenses-body" class="divide-y print:divide-black">
                            <!-- Expenses added via JS -->
                        </tbody>
                        <tfoot class="bg-muted/30 font-black print:bg-gray-50">
                            <tr>
                                <td class="p-4 text-right print:text-black">Grand Total</td>
                                <td class="p-4 text-right text-red-400 print:text-black" id="grand-total-expenses">₱
                                    0.00</td>
                                <td class="no-print"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <!-- Financial Analysis Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                <div class="space-y-6">
                    <div class="rounded-xl border p-6 bg-muted/20 space-y-4 print:border-black">
                        <h3 class="text-xs font-black uppercase tracking-widest text-muted-foreground print:text-black">
                            3. Cash on Bank</h3>
                        <div class="flex items-center gap-4">
                            <span class="text-xl font-black">₱</span>
                            <input type="number" id="cash_on_bank" step="0.01"
                                class="bg-transparent border-b-2 border-white/10 text-2xl font-black focus:border-green-500 outline-none w-full print:border-black"
                                value="<?= isset($report['cash_on_bank']) ? $report['cash_on_bank'] : '0' ?>">
                        </div>
                        <p class="text-[10px] text-muted-foreground italic print:text-gray-600">* Attachment of
                            organization passbook is required.</p>

                        <!-- File Upload -->
                        <div class="no-print p-4 rounded-lg bg-background border-2 border-dashed border-white/10 hover:border-green-500/50 transition-all text-center group cursor-pointer"
                            onclick="document.getElementById('passbook_file').click()">
                            <input type="file" id="passbook_file" class="hidden" onchange="displayFileName(this)">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-6 h-6 mx-auto text-muted-foreground group-hover:text-green-400 mb-2"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span class="text-[11px] font-bold text-muted-foreground group-hover:text-foreground"
                                id="file-name">Click to attach passbook copy</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border p-6 bg-muted/20 space-y-3 print:border-black">
                        <h3 class="text-xs font-black uppercase tracking-widest text-muted-foreground print:text-black">
                            4. Cash on Hand Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between p-2 rounded hover:bg-white/5 transition-colors">
                                <span class="text-muted-foreground print:text-gray-600">Total Collection</span>
                                <span class="font-bold print:text-black" id="total-collection-summary">₱ 0.00</span>
                            </div>
                            <div class="flex justify-between p-2 rounded hover:bg-white/5 transition-colors">
                                <span class="text-muted-foreground print:text-gray-600">Total Expenses</span>
                                <span class="font-bold print:text-black" id="total-expenses-summary">₱ 0.00</span>
                            </div>
                            <div class="flex justify-between p-2 rounded hover:bg-white/5 transition-colors">
                                <span class="text-muted-foreground print:text-gray-600">Cash on Bank</span>
                                <span class="font-bold print:text-black" id="cash-on-bank-summary">₱ 0.00</span>
                            </div>
                            <div class="pt-2 border-t border-white/10 flex justify-between p-2 print:border-black">
                                <span class="font-black print:text-black">Cash on Hand</span>
                                <span class="font-black text-green-400 print:text-black" id="cash-on-hand-result">₱
                                    0.00</span>
                            </div>
                            <div
                                class="p-3 bg-green-500/10 rounded-lg flex justify-between items-center print:bg-gray-100 mt-4">
                                <span class="text-xs font-black uppercase tracking-widest print:text-black">Total
                                    Remaining Fund</span>
                                <span class="text-xl font-black text-green-400 print:text-black"
                                    id="total-remaining-fund">₱ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures Grid -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 gap-12 mt-20 pt-12 border-t border-white/5 print:border-black print:text-black">
                <!-- Group 1 -->
                <div class="space-y-12">
                    <div class="text-center group">
                        <p class="text-[10px] font-bold uppercase text-muted-foreground mb-4 print:text-gray-600">
                            Prepared by:</p>
                        <input type="text" id="treasurer_name"
                            class="w-full text-center font-black text-lg bg-transparent border-b-2 border-white/10 focus:border-green-500 outline-none print:border-black print:text-black"
                            placeholder="Treasurer Full Name"
                            value="<?= isset($report['treasurer_name']) ? esc($report['treasurer_name']) : '' ?>">
                        <p
                            class="text-[10px] font-bold uppercase text-muted-foreground mt-2 print:text-black tracking-widest leading-none">
                            Treasurer</p>
                    </div>
                    <div class="text-center group">
                        <input type="text" id="head_name"
                            class="w-full text-center font-black text-lg bg-transparent border-b-2 border-white/10 focus:border-green-500 outline-none print:border-black print:text-black"
                            placeholder="Head of Organization"
                            value="<?= isset($report['head_name']) ? esc($report['head_name']) : '' ?>">
                        <p
                            class="text-[10px] font-bold uppercase text-muted-foreground mt-2 print:text-black tracking-widest leading-none">
                            Head of Organization</p>
                    </div>
                </div>

                <!-- Group 2 -->
                <div class="space-y-12">
                    <div class="text-center group">
                        <p class="text-[10px] font-bold uppercase text-muted-foreground mb-4 print:text-gray-600">
                            Reviewed by:</p>
                        <input type="text" id="auditor_name"
                            class="w-full text-center font-black text-lg bg-transparent border-b-2 border-white/10 focus:border-green-500 outline-none print:border-black print:text-black"
                            placeholder="Auditor Full Name"
                            value="<?= isset($report['auditor_name']) ? esc($report['auditor_name']) : '' ?>">
                        <p
                            class="text-[10px] font-bold uppercase text-muted-foreground mt-2 print:text-black tracking-widest leading-none">
                            Auditor</p>
                    </div>
                    <div class="text-center group">
                        <p class="text-[10px] font-bold uppercase text-muted-foreground mb-4 print:text-gray-600">
                            Approved:</p>
                        <input type="text" id="adviser_name"
                            class="w-full text-center font-black text-lg bg-transparent border-b-2 border-white/10 focus:border-green-500 outline-none print:border-black print:text-black"
                            placeholder="Adviser Full Name"
                            value="<?= isset($report['adviser_name']) ? esc($report['adviser_name']) : '' ?>">
                        <p
                            class="text-[10px] font-bold uppercase text-muted-foreground mt-2 print:text-black tracking-widest leading-none">
                            Adviser</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfHeaderName = '<?= esc(config('Security')->headerName) ?>';
    let csrfToken = '<?= csrf_hash() ?>';

    function updateCsrfToken(next) {
        if (typeof next === 'string' && next.length > 0) csrfToken = next;
    }

    let collections = <?= json_encode($report['collections'] ?? []) ?>;
    let expenses = <?= json_encode($report['expenses'] ?? []) ?>;

    function renderCollections() {
        const tbody = document.getElementById('collections-body');
        tbody.innerHTML = '';
        let total = 0;
        collections.forEach((item, index) => {
            const amount = parseFloat(item.amount) || 0;
            total += amount;
            tbody.innerHTML += `
                <tr class="group hover:bg-white/5 transition-colors print:hover:bg-transparent">
                    <td class="p-2">
                        <input type="text" value="${item.type || ''}" 
                            class="bg-transparent border-none focus:ring-1 focus:ring-green-500 rounded px-2 py-1 w-full text-sm font-medium print:text-black" 
                            onchange="updateCollection(${index}, 'type', this.value)" placeholder="Enter type...">
                    </td>
                    <td class="p-2">
                        <div class="flex items-center gap-2 justify-end">
                            <span class="text-muted-foreground font-bold text-[10px]">₱</span>
                            <input type="number" step="0.01" value="${amount}" 
                                class="bg-transparent border-none text-right focus:ring-1 focus:ring-green-500 rounded px-2 py-1 w-[120px] text-sm font-bold print:text-black" 
                                onchange="updateCollection(${index}, 'amount', this.value)">
                        </div>
                    </td>
                    <td class="no-print p-2 text-center">
                        <button onclick="removeCollection(${index})" class="h-8 w-8 rounded text-muted-foreground hover:text-red-400 hover:bg-red-500/10 transition-all flex items-center justify-center mx-auto opacity-0 group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                </tr>
            `;
        });
        document.getElementById('grand-total-collection').textContent = '₱ ' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
        updateSummary();
    }

    function renderExpenses() {
        const tbody = document.getElementById('expenses-body');
        tbody.innerHTML = '';
        let total = 0;
        expenses.forEach((item, index) => {
            const amount = parseFloat(item.amount) || 0;
            total += amount;
            tbody.innerHTML += `
                <tr class="group hover:bg-white/5 transition-colors print:hover:bg-transparent">
                    <td class="p-2">
                        <input type="text" value="${item.activity || ''}" 
                            class="bg-transparent border-none focus:ring-1 focus:ring-green-500 rounded px-2 py-1 w-full text-sm font-medium print:text-black" 
                            onchange="updateExpense(${index}, 'activity', this.value)" placeholder="Enter activity...">
                    </td>
                    <td class="p-2">
                        <div class="flex items-center gap-2 justify-end">
                            <span class="text-muted-foreground font-bold text-[10px]">₱</span>
                            <input type="number" step="0.01" value="${amount}" 
                                class="bg-transparent border-none text-right focus:ring-1 focus:ring-green-500 rounded px-2 py-1 w-[120px] text-sm font-bold print:text-black" 
                                onchange="updateExpense(${index}, 'amount', this.value)">
                        </div>
                    </td>
                    <td class="no-print p-2 text-center">
                        <button onclick="removeExpense(${index})" class="h-8 w-8 rounded text-muted-foreground hover:text-red-400 hover:bg-red-500/10 transition-all flex items-center justify-center mx-auto opacity-0 group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                </tr>
            `;
        });
        document.getElementById('grand-total-expenses').textContent = '₱ ' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
        updateSummary();
    }

    function addCollectionRow() { collections.push({ type: '', amount: 0 }); renderCollections(); }
    function addExpenseRow() { expenses.push({ activity: '', amount: 0 }); renderExpenses(); }
    function removeCollection(i) { collections.splice(i, 1); renderCollections(); }
    function removeExpense(i) { expenses.splice(i, 1); renderExpenses(); }
    function updateCollection(i, f, v) { collections[i][f] = v; renderCollections(); }
    function updateExpense(i, f, v) { expenses[i][f] = v; renderExpenses(); }

    function updateSummary() {
        let totalCollection = collections.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        let totalExpenses = expenses.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        let cashOnBank = parseFloat(document.getElementById('cash_on_bank').value) || 0;
        let cashOnHand = totalCollection - totalExpenses - cashOnBank;
        let totalRemaining = cashOnBank + cashOnHand;

        const fmt = (v) => '₱ ' + v.toLocaleString('en-US', { minimumFractionDigits: 2 });
        document.getElementById('total-collection-summary').textContent = fmt(totalCollection);
        document.getElementById('total-expenses-summary').textContent = fmt(totalExpenses);
        document.getElementById('cash-on-bank-summary').textContent = fmt(cashOnBank);
        document.getElementById('cash-on-hand-result').textContent = fmt(cashOnHand);
        document.getElementById('total-remaining-fund').textContent = fmt(totalRemaining);
    }

    document.getElementById('cash_on_bank').addEventListener('input', updateSummary);

    function displayFileName(input) {
        const fileName = input.files[0]?.name;
        if (fileName) document.getElementById('file-name').textContent = 'Selected: ' + fileName;
    }

    function saveForm() {
        const formData = new FormData();
        formData.append('academic_year', document.getElementById('academic_year').value);
        formData.append('collections', JSON.stringify(collections));
        formData.append('expenses', JSON.stringify(expenses));
        formData.append('cash_on_bank', document.getElementById('cash_on_bank').value);
        formData.append('treasurer_name', document.getElementById('treasurer_name').value);
        formData.append('auditor_name', document.getElementById('auditor_name').value);
        formData.append('head_name', document.getElementById('head_name').value);
        formData.append('adviser_name', document.getElementById('adviser_name').value);
        const file = document.getElementById('passbook_file').files[0];
        if (file) formData.append('passbook_copy', file);

        fetch('/organization/financial-report/store', {
            method: 'POST',
            headers: { [csrfHeaderName]: csrfToken },
            body: formData
        })
            .then(r => r.json())
            .then(data => {
                updateCsrfToken(data.csrf);
                if (data.success) { alert('Financial report saved successfully!'); location.reload(); }
                else { alert('Error: ' + (data.message || 'Failed to save report')); }
            });
    }

    function downloadPDF() {
        const ay = document.getElementById('academic_year').value;
        if (!ay) { alert('Please enter academic year and save first'); return; }
        window.location.href = `/organization/financial-report/download/${ay}`;
    }

    if (collections.length === 0) addCollectionRow(); else renderCollections();
    if (expenses.length === 0) addExpenseRow(); else renderExpenses();
</script>

<?= $this->endSection() ?>