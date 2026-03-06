<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Admin Overview</h1>
        <p class="text-muted-foreground">Monitor system progress and accreditation status.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground uppercase">Total Organizations</h3>
                <i data-lucide="building-2" class="w-4 h-4 text-muted-foreground opacity-70"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-3xl font-bold">
                    <?= $total_organizations ?>
                </div>
                <p class="text-xs text-muted-foreground mt-1">+2% from last month</p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground uppercase">Total Documents</h3>
                <i data-lucide="file-text" class="w-4 h-4 text-muted-foreground opacity-70"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-3xl font-bold">
                    <?= $total_documents ?>
                </div>
                <p class="text-xs text-muted-foreground mt-1">+12% from last month</p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground uppercase">Pending Reviews</h3>
                <i data-lucide="hourglass" class="w-4 h-4 text-muted-foreground opacity-70"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-3xl font-bold text-destructive">
                    <?= $pending_documents ?>
                </div>
                <p class="text-xs text-muted-foreground mt-1">Needs attention</p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground uppercase">Active Year</h3>
                <i data-lucide="calendar" class="w-4 h-4 text-muted-foreground opacity-70"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-3xl font-bold">
                    <?= date('Y') ?>
                </div>
                <p class="text-xs text-muted-foreground mt-1">A.Y. Baseline</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 grid-cols-1 md:grid-cols-7">
        <div class="col-span-1 md:col-span-4 rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6 pb-2">
                <h3 class="font-semibold leading-none tracking-tight">Submission Volume</h3>
                <p class="text-sm text-muted-foreground">Overview of monthly document submissions.</p>
            </div>
            <div class="p-6 h-[350px]">
                <canvas id="submissionsChart"></canvas>
            </div>
        </div>
        <div class="col-span-1 md:col-span-3 rounded-xl border bg-card text-card-foreground shadow">
            <div class="p-6 pb-2">
                <h3 class="font-semibold leading-none tracking-tight">System Info</h3>
                <p class="text-sm text-muted-foreground">Quick stats about the current setup.</p>
            </div>
            <div class="p-6 space-y-8">
                <div class="flex items-center">
                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center mr-4">
                        <i data-lucide="school" class="w-5 h-5 text-primary"></i>
                    </div>
                    <div class="flex-1 space-y-1">
                        <p class="text-sm font-medium leading-none">Campus Count</p>
                        <p class="text-xs text-muted-foreground">Isulan, Access, Tacurong, etc.</p>
                    </div>
                    <div class="ml-auto font-medium">7</div>
                </div>
                <div class="flex items-center">
                    <div class="w-9 h-9 rounded-full bg-secondary/10 flex items-center justify-center mr-4">
                        <i data-lucide="folder-check" class="w-5 h-5 text-secondary"></i>
                    </div>
                    <div class="flex-1 space-y-1">
                        <p class="text-sm font-medium leading-none">Accreditation Progress</p>
                        <p class="text-xs text-muted-foreground">Mandatory requirements verified.</p>
                    </div>
                    <div class="ml-auto font-medium">85%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border bg-card text-card-foreground shadow overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="font-semibold leading-none tracking-tight">Recent Submissions</h3>
            <p class="text-sm text-muted-foreground">Latest documents uploaded by organizations.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 border-b">
                    <tr>
                        <th class="h-10 px-4 text-left font-medium text-muted-foreground">Organization</th>
                        <th class="h-10 px-4 text-left font-medium text-muted-foreground">Document</th>
                        <th class="h-10 px-4 text-left font-medium text-muted-foreground text-center">Date</th>
                        <th class="h-10 px-4 text-right font-medium text-muted-foreground">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($recent_submissions)): ?>
                        <tr>
                            <td colspan="4" class="p-8 text-center text-muted-foreground italic">No recent submissions
                                found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_submissions as $submission): ?>
                            <tr class="hover:bg-muted/30 transition-colors">
                                <td class="p-4 font-medium">
                                    <?= esc($submission['org_name']) ?>
                                </td>
                                <td class="p-4 text-muted-foreground">
                                    <?= esc($submission['document_title']) ?>
                                </td>
                                <td class="p-4 text-center text-muted-foreground">
                                    <?= date('M d, Y', strtotime($submission['created_at'])) ?>
                                </td>
                                <td class="p-4 text-right">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        <?= $submission['status'] === 'approved' ? 'bg-green-100 text-green-800' :
                                            ($submission['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                        <?= ucfirst($submission['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('submissionsChart').getContext('2d');
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(44, 62, 80, 0.4)');
        gradient.addColorStop(1, 'rgba(44, 62, 80, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: 'Submissions',
                    data: <?= json_encode($chart_data) ?>,
                    borderColor: '#2c3e50',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#2c3e50',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        titleColor: '#F8FAFC',
                        bodyColor: '#F8FAFC',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94A3B8', font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: { precision: 0, color: '#94A3B8' }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>