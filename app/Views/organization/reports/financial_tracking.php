<?= $this->extend('layouts/org_modern') ?>
<?php $title = 'Financial Tracking'; ?>

<?= $this->section('content') ?>

<div class="space-y-6 max-w-7xl mx-auto pb-10">
    <!-- Page Header -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-card p-6 rounded-2xl border shadow-sm">
        <div class="space-y-1">
            <h2 class="text-2xl font-black tracking-tight">Financial Tracking</h2>
            <div class="flex items-center gap-2">
                <span
                    class="text-xs font-bold px-2 py-0.5 rounded bg-green-500/10 text-green-400 border border-green-500/20">
                    <?= esc($organization['name'] ?? 'Organization') ?>
                </span>
                <p class="text-xs text-muted-foreground">Track yearly financial performance and comparative analytics.
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('organization/financial-report') ?>"
                class="inline-flex items-center h-10 px-4 rounded-xl bg-green-600 text-white text-xs font-black hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Report
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar: Year Selection -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-card rounded-2xl border p-6 space-y-6 shadow-sm">
                <div class="space-y-1">
                    <h3 class="text-sm font-black uppercase tracking-widest text-muted-foreground">Select Years</h3>
                    <p class="text-[10px] text-muted-foreground">Compare up to 5 academic years of data.</p>
                </div>

                <?php if (empty($years)): ?>
                    <div class="py-12 flex flex-col items-center justify-center text-center space-y-4">
                        <div class="w-12 h-12 rounded-full bg-muted/30 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-muted-foreground opacity-20"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-xs text-muted-foreground italic">No financial reports found yet.</p>
                    </div>
                <?php else: ?>
                    <form id="compareForm" class="space-y-6">
                        <?= csrf_field() ?>
                        <div class="grid grid-cols-1 gap-2 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            <?php foreach ($years as $yr): ?>
                                <label
                                    class="group flex items-center justify-between p-3 rounded-xl border border-white/5 bg-muted/20 hover:border-green-500/30 hover:bg-green-500/5 transition-all cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" name="years[]" value="<?= esc($yr) ?>"
                                                class="w-5 h-5 rounded border-white/10 bg-background checked:bg-green-600 transition-all cursor-pointer">
                                        </div>
                                        <span class="text-sm font-bold truncate"><?= esc($yr) ?></span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-4 h-4 text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit"
                            class="w-full h-11 rounded-xl bg-blue-600 text-white text-xs font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Compare Selected
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Chart & Summary -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Chart Card -->
            <div class="bg-card rounded-2xl border p-6 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div class="space-y-1">
                        <h3 class="text-sm font-black uppercase tracking-widest text-muted-foreground">Financial
                            Variance</h3>
                        <p class="text-[10px] text-muted-foreground">Comparative analysis of Collections, Expenses, and
                            Funds.</p>
                    </div>
                </div>
                <div class="relative h-[350px]">
                    <canvas id="comparisonChart"></canvas>
                    <div id="chartLoader"
                        class="hidden absolute inset-0 flex items-center justify-center bg-card/50 backdrop-blur-sm z-10">
                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="w-8 h-8 border-4 border-green-600 border-t-transparent rounded-full animate-spin">
                            </div>
                            <span class="text-xs font-bold text-muted-foreground uppercase tracking-widest">Analyzing
                                Data...</span>
                        </div>
                    </div>
                    <div id="chartPlaceholder"
                        class="absolute inset-0 flex flex-col items-center justify-center text-center space-y-4 opacity-30">
                        <div class="w-20 h-20 rounded-full bg-muted/30 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-widest">Select years to see visualization</p>
                    </div>
                </div>
            </div>

            <!-- Summary Table Card -->
            <div class="bg-card rounded-2xl border overflow-hidden shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-sm font-black uppercase tracking-widest text-muted-foreground">Historical Summary
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/30">
                            <tr>
                                <th
                                    class="h-12 px-6 text-left font-bold text-muted-foreground uppercase tracking-tighter text-[10px]">
                                    Academic Year</th>
                                <th
                                    class="h-12 px-6 text-right font-bold text-muted-foreground uppercase tracking-tighter text-[10px]">
                                    Collection</th>
                                <th
                                    class="h-12 px-6 text-right font-bold text-muted-foreground uppercase tracking-tighter text-[10px]">
                                    Expenses</th>
                                <th
                                    class="h-12 px-6 text-right font-bold text-muted-foreground uppercase tracking-tighter text-[10px]">
                                    Bank Balance</th>
                                <th
                                    class="h-12 px-6 text-right font-bold text-muted-foreground uppercase tracking-tighter text-[10px]">
                                    Remaining</th>
                                <th
                                    class="h-12 px-6 text-center font-bold text-muted-foreground uppercase tracking-tighter text-[10px]">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-muted-foreground italic">No historical data
                                        available.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reports as $r): ?>
                                    <tr class="group hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-black text-foreground"><?= esc($r['academic_year'] ?? '') ?>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">₱
                                            <?= number_format($r['total_collection'] ?? 0, 2) ?></td>
                                        <td class="px-6 py-4 text-right font-medium text-red-400">₱
                                            <?= number_format($r['total_expenses'] ?? 0, 2) ?></td>
                                        <td class="px-6 py-4 text-right font-medium text-muted-foreground italic">₱
                                            <?= number_format($r['cash_on_bank'] ?? 0, 2) ?></td>
                                        <td class="px-6 py-4 text-right font-black text-green-400">₱
                                            <?= number_format($r['total_remaining_fund'] ?? 0, 2) ?></td>
                                        <td class="px-6 py-4 text-center">
                                            <?php if (!empty($r['id'])): ?>
                                                <a href="<?= base_url('organization/financial-report/download/' . $r['id']) ?>"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-muted text-muted-foreground hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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

    let chart;
    function renderChart(rows) {
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        const labels = rows.map(r => r.year);
        const collections = rows.map(r => Number(r.total_collection || 0));
        const expenses = rows.map(r => Number(r.total_expenses || 0));
        const remaining = rows.map(r => Number(r.remaining_fund || 0));

        if (chart) chart.destroy();

        const isDark = true; // Based on theme
        const gridColor = 'rgba(255, 255, 255, 0.05)';
        const textColor = 'rgba(255, 255, 255, 0.6)';

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Collections',
                        data: collections,
                        backgroundColor: '#22c55e',
                        borderRadius: 6,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Expenses',
                        data: expenses,
                        backgroundColor: '#ef4444',
                        borderRadius: 6,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Rem. Fund',
                        data: remaining,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        barPercentage: 0.6
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            font: { weight: 'bold', size: 10 },
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleFont: { weight: 'black', size: 14 },
                        bodyFont: { weight: 'bold' },
                        callbacks: {
                            label: (context) => context.dataset.label + ': ' + '₱ ' + context.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { weight: 'bold' } }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            font: { size: 10 },
                            callback: (v) => '₱' + (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v)
                        }
                    }
                }
            }
        });
    }

    const form = document.getElementById('compareForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const years = Array.from(new FormData(form).getAll('years[]'));
            if (years.length === 0) { alert('Select at least one year'); return; }

            document.getElementById('chartLoader').classList.remove('hidden');
            document.getElementById('chartPlaceholder').classList.add('hidden');

            fetch('/organization/financial-report/comparison', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeaderName]: csrfToken
                },
                body: JSON.stringify({ years })
            })
                .then(r => r.json())
                .then(result => {
                    updateCsrfToken(result.csrf);
                    document.getElementById('chartLoader').classList.add('hidden');
                    if (result.success) renderChart(result.data || []);
                    else alert(result.message || 'Analysis failed');
                })
                .catch(err => {
                    document.getElementById('chartLoader').classList.add('hidden');
                    console.error(err);
                    alert('Analysis error');
                });
        });
    }
</script>

<?= $this->endSection() ?>