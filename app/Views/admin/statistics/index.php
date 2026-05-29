<?= $this->extend('layouts/admin_modern') ?>
<?php $title = 'Accreditation Statistics & Compliance'; ?>

<?= $this->section('content') ?>

<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-slide-up animation-delay-100">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-white">Accreditation Statistics</h2>
            <p class="text-muted-foreground mt-1">Real-time tracking of organization accreditations, campus progress rates, and compliance criteria gaps.</p>
        </div>
    </div>

    <!-- React Statistics Overhaul Mount Point -->
    <div id="react-statistics-dashboard" class="animate-slide-up animation-delay-200" data-props="<?= htmlspecialchars(json_encode([
        'campuses' => \App\Models\OrganizationModel::CAMPUSES
    ]), ENT_QUOTES, 'UTF-8') ?>"></div>

    <!-- Comparative Analytics Section -->
    <div class="rounded-xl border border-white/5 bg-card p-6 shadow-sm animate-slide-up animation-delay-300">
        <h3 class="text-lg font-bold text-white mb-1">Comparative Financial Analytics</h3>
        <p class="text-muted-foreground text-sm mb-6">Select multiple student organizations and academic years to graph collection vs. expenditure trends.</p>

        <form id="comparisonForm">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Orgs selection -->
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-slate-200">Select Student Organizations:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        <?php foreach ($organizations as $org): ?>
                            <label class="flex items-center gap-3 rounded-lg border border-white/5 px-3 py-2 cursor-pointer hover:bg-white/[0.03] transition-colors text-sm text-slate-300">
                                <input type="checkbox" name="organizations[]" value="<?= $org['id'] ?>" class="rounded border-white/10 text-blue-600 bg-background focus:ring-blue-500">
                                <span class="truncate"><?= esc($org['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Academic years selection -->
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-slate-200">Select Financial Report Years:</p>
                    <?php if (empty($years)): ?>
                        <p class="text-xs text-muted-foreground py-2">No academic years found. Submit Audited Financial Reports first.</p>
                    <?php else: ?>
                        <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                            <?php foreach ($years as $yr): ?>
                                <label class="flex items-center gap-3 rounded-lg border border-white/5 px-3 py-2 cursor-pointer hover:bg-white/[0.03] transition-colors text-sm text-slate-300">
                                    <input type="checkbox" name="years[]" value="<?= esc($yr) ?>" class="rounded border-white/10 text-blue-600 bg-background focus:ring-blue-500">
                                    <span><?= esc($yr) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Metric Selection & Button -->
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 border-t border-white/5 pt-5">
                <div class="w-full sm:w-72">
                    <label class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block mb-2" for="metric">Analysis Metric</label>
                    <select id="metric" class="h-10 rounded-md border border-white/10 bg-background text-foreground px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full">
                        <option value="collection">Total Funds Collected (₱)</option>
                        <option value="expenses">Total Operating Expenses (₱)</option>
                        <option value="remaining">Remaining Reserve Funds (₱)</option>
                    </select>
                </div>

                <button type="submit" class="inline-flex items-center justify-center h-10 px-5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all gap-2 shadow-lg shadow-blue-900/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Generate Comparison Chart
                </button>
            </div>
        </form>

        <!-- Chart Output container -->
        <div id="comparisonResults" style="display:none;" class="mt-8 pt-6 border-t border-white/5 animate-slide-up">
            <h4 class="text-base font-bold text-white mb-4">Comparative Financial Analysis</h4>
            <div class="bg-card p-4 rounded-xl border border-white/5 mb-6">
                <canvas id="comparisonChart" height="120"></canvas>
            </div>
            <div class="overflow-x-auto rounded-xl border border-white/5">
                <table id="comparisonTable" class="w-full text-sm">
                    <thead>
                        <tr id="comparsionTableHead" class="bg-muted/50 border-b border-white/5"></tr>
                    </thead>
                    <tbody id="comparisonTableBody" class="divide-y divide-white/5"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const csrfHeaderName = '<?= esc(config('Security')->headerName) ?>';
    let csrfToken = '<?= csrf_hash() ?>';

    function updateCsrfToken(next) {
        if (typeof next === 'string' && next.length > 0) csrfToken = next;
    }

    function formatCurrency(value) {
        return '₱' + Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Chart.js defaults for dark mode
    Chart.defaults.color = 'rgba(255,255,255,0.6)';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';

    let comparisonChartInstance = null;
    const palette = ['#3b82f6', '#10b981', '#f97316', '#a855f7', '#ef4444', '#14b8a6', '#eab308'];

    document.getElementById('comparisonForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = { organizations: formData.getAll('organizations[]'), years: formData.getAll('years[]') };
        if (!data.organizations.length || !data.years.length) { 
            alert('Please select at least one student organization and one year.'); 
            return; 
        }

        fetch('/admin/statistics/comparison', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: csrfToken },
            body: JSON.stringify(data)
        }).then(r => r.json()).then(result => {
            updateCsrfToken(result.csrf);
            if (result.success) {
                const years = [...data.years].reverse();
                const metric = document.getElementById('metric').value;
                
                // Draw Chart
                const datasets = result.data.map((org, idx) => ({
                    label: org.name,
                    data: years.map(y => org.years && org.years[y] ? Number(org.years[y][metric] || 0) : 0),
                    backgroundColor: palette[idx % palette.length] + '33',
                    borderColor: palette[idx % palette.length],
                    borderWidth: 2.5, 
                    tension: 0.35, 
                    fill: false, 
                    pointRadius: 4
                }));

                if (comparisonChartInstance) comparisonChartInstance.destroy();
                comparisonChartInstance = new Chart(document.getElementById('comparisonChart').getContext('2d'), {
                    type: 'line',
                    data: { labels: years, datasets },
                    options: { 
                        responsive: true, 
                        plugins: { tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + formatCurrency(ctx.raw) } } }, 
                        scales: { y: { beginAtZero: true, ticks: { callback: v => formatCurrency(v) } } } 
                    }
                });

                // Generate table data
                let head = '<th class="h-10 px-4 text-left font-medium text-muted-foreground">Organization</th>' + years.map(y => `<th class="h-10 px-4 text-left font-medium text-muted-foreground">${y}</th>`).join('');
                document.getElementById('comparsionTableHead').innerHTML = head;
                let body = result.data.map(org => `<tr class="hover:bg-white/[0.01] transition-colors"><td class="p-4 font-semibold text-white">${org.name}</td>${years.map(y => `<td class="p-4 text-muted-foreground">${formatCurrency(org.years && org.years[y] ? org.years[y][metric] : 0)}</td>`).join('')}</tr>`).join('');
                document.getElementById('comparisonTableBody').innerHTML = body;
                document.getElementById('comparisonResults').style.display = 'block';
            } else { 
                alert(result.message); 
            }
        });
    });
</script>

<?= $this->endSection() ?>