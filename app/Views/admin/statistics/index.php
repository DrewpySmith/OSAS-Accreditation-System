<?= $this->extend('layouts/admin_modern') ?>
<?php $title = 'Statistics'; ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="animate-slide-up animation-delay-100">
        <h2 class="text-3xl font-bold tracking-tight">Statistics</h2>
        <p class="text-muted-foreground mt-1">Overview of all organization activities and financial comparisons.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-slide-up animation-delay-200">
        <div class="rounded-xl border bg-card p-5 shadow-sm">
            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1">Uploaded Docs (This Year)
            </p>
            <p class="text-4xl font-bold text-blue-400"><?= number_format($uploaded_docs_this_year ?? 0) ?></p>
        </div>
        <div class="rounded-xl border bg-card p-5 shadow-sm">
            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1">Total Organizations</p>
            <p class="text-4xl font-bold text-green-400"><?= number_format($total_orgs ?? 0) ?></p>
        </div>
        <div class="rounded-xl border bg-card p-5 shadow-sm">
            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1">Total Activities</p>
            <p class="text-4xl font-bold text-orange-400">
                <?= number_format(array_sum(array_column($activities_per_org ?? [], 'count'))) ?>
            </p>
        </div>
    </div>

    <!-- Activities Chart -->
    <div class="rounded-xl border bg-card p-6 shadow-sm animate-slide-up animation-delay-300">
        <h3 class="text-lg font-semibold mb-4">Activities per Organization</h3>
        <canvas id="activitiesChart" height="100"></canvas>
    </div>

    <!-- Organization Cards (React modal) -->
    <div class="rounded-xl border bg-card p-6 shadow-sm animate-slide-up animation-delay-400">
        <h3 class="text-lg font-semibold mb-1">Select Organization to View Details</h3>
        <p class="text-muted-foreground text-sm mb-5">Click on any organization card to see their detailed statistics.
        </p>
        <div
            id="react-statistics-org-cards"
            data-props="<?= htmlspecialchars(json_encode(['organizations' => array_map(fn($o) => ['id' => $o['id'], 'name' => $o['name'], 'acronym' => $o['acronym'] ?? '', 'campus' => $o['campus'] ?? ''], $organizations ?? [])]), ENT_QUOTES) ?>"
        ></div>
    </div>

    <!-- Comparison Tool -->
    <div class="rounded-xl border bg-card p-6 shadow-sm animate-slide-up animation-delay-500">
        <h3 class="text-lg font-semibold mb-1">Compare Multiple Organizations</h3>
        <p class="text-muted-foreground text-sm mb-5">Select organizations and years to generate a financial comparison
            chart.</p>

        <form id="comparisonForm">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                <div>
                    <p class="text-sm font-medium mb-3">Select Organizations:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <?php foreach ($organizations as $org): ?>
                            <label
                                class="flex items-center gap-2 rounded-md border border-white/10 px-3 py-2.5 cursor-pointer hover:bg-muted/50 transition-colors text-sm">
                                <input type="checkbox" name="organizations[]" value="<?= $org['id'] ?>"
                                    class="rounded border-white/20 text-blue-600">
                                <?= esc($org['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium mb-3">Select Years:</p>
                    <?php if (empty($years)): ?>
                        <p class="text-sm text-muted-foreground">No academic years found from financial reports yet.</p>
                    <?php else: ?>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($years as $yr): ?>
                                <label
                                    class="flex items-center gap-2 rounded-md border border-white/10 px-3 py-2.5 cursor-pointer hover:bg-muted/50 transition-colors text-sm">
                                    <input type="checkbox" name="years[]" value="<?= esc($yr) ?>"
                                        class="rounded border-white/20 text-blue-600">
                                    <?= esc($yr) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-5">
                <label class="text-sm font-medium mb-2 block" for="metric">Metric:</label>
                <select id="metric"
                    class="h-9 rounded-md border border-white/10 bg-background text-foreground px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full sm:w-64">
                    <option value="collection">Total Collection</option>
                    <option value="expenses">Total Expenses</option>
                    <option value="remaining">Remaining Fund</option>
                </select>
                <p class="text-xs text-muted-foreground mt-1.5">Chart shows one line per organization across the
                    selected years.</p>
            </div>

            <button type="submit"
                class="inline-flex items-center h-10 px-5 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Generate Comparison
            </button>
        </form>

        <div id="comparisonResults" style="display:none;" class="mt-8">
            <h4 class="text-base font-semibold mb-4">Comparison Results</h4>
            <canvas id="comparisonChart" height="120"></canvas>
            <div class="mt-6 overflow-x-auto">
                <table id="comparisonTable" class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr id="comparsionTableHead"></tr>
                    </thead>
                    <tbody id="comparisonTableBody"></tbody>
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
        return '₱ ' + Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Chart.js defaults for dark mode
    Chart.defaults.color = 'rgba(255,255,255,0.6)';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';

    // Activities Chart
    (function () {
        const activitiesData = <?= json_encode($activities_per_org ?? []) ?>;
        new Chart(document.getElementById('activitiesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: activitiesData.map(i => i.name),
                datasets: [{
                    label: 'Number of Activities',
                    data: activitiesData.map(i => i.count),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1.5,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    })();

    // Comparison Form
    let comparisonChartInstance = null;
    const metricLabel = m => ({ expenses: 'Total Expenses', remaining: 'Remaining Fund' }[m] ?? 'Total Collection');
    const palette = ['#3b82f6', '#22c55e', '#f97316', '#a855f7', '#ef4444', '#14b8a6', '#eab308'];

    document.getElementById('comparisonForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = { organizations: formData.getAll('organizations[]'), years: formData.getAll('years[]') };
        if (!data.organizations.length || !data.years.length) { alert('Select at least one organization and one year.'); return; }

        fetch('/admin/statistics/comparison', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: csrfToken },
            body: JSON.stringify(data)
        }).then(r => r.json()).then(result => {
            updateCsrfToken(result.csrf);
            if (result.success) {
                const years = [...data.years].reverse();
                const metric = document.getElementById('metric').value;
                // Draw chart
                const datasets = result.data.map((org, idx) => ({
                    label: org.name,
                    data: years.map(y => org.years && org.years[y] ? Number(org.years[y][metric] || 0) : 0),
                    backgroundColor: palette[idx % palette.length] + '33',
                    borderColor: palette[idx % palette.length],
                    borderWidth: 2, tension: 0.3, fill: false, pointRadius: 4
                }));
                if (comparisonChartInstance) comparisonChartInstance.destroy();
                comparisonChartInstance = new Chart(document.getElementById('comparisonChart').getContext('2d'), {
                    type: 'line',
                    data: { labels: years, datasets },
                    options: { responsive: true, plugins: { tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + formatCurrency(ctx.raw) } } }, scales: { y: { beginAtZero: true, ticks: { callback: v => formatCurrency(v) } } } }
                });
                // Draw table
                let head = '<th class="h-10 px-4 text-left font-medium text-muted-foreground">Organization</th>' + years.map(y => `<th class="h-10 px-4 text-left font-medium text-muted-foreground">${y}</th>`).join('');
                document.getElementById('comparsionTableHead').innerHTML = head;
                let body = result.data.map(org => `<tr class="border-b border-white/5"><td class="p-4 font-semibold">${org.name}</td>${years.map(y => `<td class="p-4 text-muted-foreground">${formatCurrency(org.years && org.years[y] ? org.years[y][metric] : 0)}</td>`).join('')}</tr>`).join('');
                document.getElementById('comparisonTableBody').innerHTML = body;
                document.getElementById('comparisonResults').style.display = 'block';
            } else { alert(result.message); }
        });
    });
</script>

<?= $this->endSection() ?>