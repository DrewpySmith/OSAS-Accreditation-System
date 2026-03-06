<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admin Dashboard - USG Accreditation</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <meta name="<?= csrf_header() ?>" content="<?= csrf_hash() ?>">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#2c3e50", // Admin Blue from agents.md
                        "background-light": "#FFFFFF",
                        "background-dark": "#0F172A",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1E293B",
                        "border-light": "#E2E8F0",
                        "border-dark": "#334155",
                        "text-main-light": "#111827",
                        "text-main-dark": "#F8FAFC",
                        "text-sub-light": "#64748B",
                        "text-sub-dark": "#94A3B8",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                        display: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        'card': "0.75rem",
                    },
                    boxShadow: {
                        'soft': '0 2px 10px rgba(0, 0, 0, 0.03)',
                    }
                },
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 dark:bg-background-dark font-sans transition-colors duration-200 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="w-64 flex flex-col border-r border-border-light dark:border-border-dark bg-background-light dark:bg-surface-dark flex-shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-transparent dark:border-border-dark">
                <div
                    class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-primary/30">
                    <span class="material-icons-outlined text-white text-xl">school</span>
                </div>
                <span
                    class="font-semibold text-lg text-text-main-light dark:text-text-main-dark tracking-tight">Accredify</span>
            </div>
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md bg-gray-100 dark:bg-gray-800 text-primary dark:text-white"
                    href="<?= base_url('admin/dashboard') ?>">
                    <span class="material-icons-outlined mr-3 text-xl text-primary dark:text-white">dashboard</span>
                    Overview
                </a>
                <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-text-sub-light dark:text-text-sub-dark hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-text-main-light dark:hover:text-text-main-dark transition-colors"
                    href="<?= base_url('admin/organizations') ?>">
                    <span
                        class="material-icons-outlined mr-3 text-xl text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300">groups</span>
                    Organizations
                </a>
                <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-text-sub-light dark:text-text-sub-dark hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-text-main-light dark:hover:text-text-main-dark transition-colors"
                    href="<?= base_url('admin/documents') ?>">
                    <span
                        class="material-icons-outlined mr-3 text-xl text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300">description</span>
                    Review Documents
                </a>
                <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-text-sub-light dark:text-text-sub-dark hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-text-main-light dark:hover:text-text-main-dark transition-colors"
                    href="<?= base_url('admin/statistics') ?>">
                    <span
                        class="material-icons-outlined mr-3 text-xl text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300">bar_chart</span>
                    Statistics
                </a>
            </nav>
            <div class="px-3 py-4 border-t border-border-light dark:border-border-dark">
                <div class="mb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    System
                </div>
                <a class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-text-sub-light dark:text-text-sub-dark hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-text-main-light dark:hover:text-text-main-dark transition-colors"
                    href="<?= base_url('logout') ?>">
                    <span class="material-icons-outlined mr-3 text-xl text-gray-400">logout</span>
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden bg-background-light dark:bg-background-dark relative">
            <header
                class="h-16 flex items-center justify-between px-8 border-b border-border-light dark:border-border-dark bg-background-light dark:bg-surface-dark z-10">
                <div class="flex-1 max-w-2xl">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span
                                class="material-icons-outlined text-gray-400 text-lg group-focus-within:text-primary transition-colors">search</span>
                        </div>
                        <input
                            class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg leading-5 bg-gray-50 dark:bg-gray-800 text-text-main-light dark:text-text-main-dark placeholder-gray-400 focus:outline-none focus:bg-white dark:focus:bg-gray-900 focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-all shadow-sm"
                            placeholder="Search for organizations, students, or documents..." type="text" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span
                                class="text-gray-400 text-xs border border-gray-200 dark:border-gray-600 rounded px-1.5 py-0.5">⌘
                                K</span>
                        </div>
                    </div>
                </div>
                <div class="ml-4 flex items-center space-x-4">
                    <div class="user-info flex items-center space-x-3">
                        <span class="text-sm font-medium text-text-main-light dark:text-text-main-dark">Welcome,
                            <?= session()->get('username') ?></span>
                    </div>
                    <div
                        class="h-9 w-9 rounded-full bg-gradient-to-br from-gray-500 to-gray-700 p-[2px] cursor-pointer">
                        <div
                            class="h-full w-full rounded-full bg-white dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                            <span class="material-icons-outlined text-gray-500">account_circle</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-normal text-text-main-light dark:text-text-main-dark mb-1">Admin Overview
                    </h1>
                    <p class="text-text-sub-light dark:text-text-sub-dark text-sm">Monitor system progress and
                        accreditation status.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div
                        class="bg-white dark:bg-surface-dark rounded-card border border-border-light dark:border-border-dark shadow-soft p-6 animate-slide-up animation-delay-100 hover:-translate-y-1 transition-transform duration-300">
                        <h3 class="text-text-sub-light dark:text-text-sub-dark font-medium text-sm mb-2">Total
                            Organizations</h3>
                        <div
                            class="text-4xl font-semibold text-text-main-light dark:text-text-main-dark tracking-tight">
                            <?= $total_organizations ?>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-surface-dark rounded-card border border-border-light dark:border-border-dark shadow-soft p-6 animate-slide-up animation-delay-200 hover:-translate-y-1 transition-transform duration-300">
                        <h3 class="text-text-sub-light dark:text-text-sub-dark font-medium text-sm mb-2">Total Documents
                        </h3>
                        <div
                            class="text-4xl font-semibold text-text-main-light dark:text-text-main-dark tracking-tight">
                            <?= $total_documents ?>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-surface-dark rounded-card border border-border-light dark:border-border-dark shadow-soft p-6 animate-slide-up animation-delay-300 hover:-translate-y-1 transition-transform duration-300">
                        <h3 class="text-text-sub-light dark:text-text-sub-dark font-medium text-sm mb-2">Pending Reviews
                        </h3>
                        <div
                            class="text-4xl font-semibold text-text-main-light dark:text-text-main-dark tracking-tight">
                            <?= $pending_documents ?>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div
                        class="lg:col-span-2 bg-white dark:bg-surface-dark rounded-card border border-border-light dark:border-border-dark shadow-soft p-6 flex flex-col min-h-[400px] animate-slide-up animation-delay-400">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-text-sub-light dark:text-text-sub-dark font-medium">Submission Volume</h2>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                                <span
                                    class="px-3 py-1 bg-white dark:bg-surface-dark shadow-sm rounded-md text-xs font-medium text-text-main-light dark:text-text-main-dark">Monthly</span>
                            </div>
                        </div>
                        <div class="relative h-64 w-full mt-4">
                            <canvas id="submissionsChart"></canvas>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-surface-dark rounded-card border border-border-light dark:border-border-dark shadow-soft p-6 flex flex-col">
                        <h2 class="text-text-sub-light dark:text-text-sub-dark font-medium mb-6">System Info</h2>
                        <div class="space-y-4">
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-border-light dark:border-border-dark">
                                <p
                                    class="text-xs text-text-sub-light dark:text-text-sub-dark uppercase font-bold tracking-wider mb-1">
                                    Active Year</p>
                                <p class="text-xl font-semibold text-text-main-light dark:text-text-main-dark">
                                    <?= date('Y') ?>
                                </p>
                            </div>
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-border-light dark:border-border-dark">
                                <p
                                    class="text-xs text-text-sub-light dark:text-text-sub-dark uppercase font-bold tracking-wider mb-1">
                                    Campus Count</p>
                                <p class="text-xl font-semibold text-text-main-light dark:text-text-main-dark">7</p>
                                <p class="text-xs text-text-sub-light mt-1">Isulan, Access, Tacurong, etc.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-medium text-text-main-light dark:text-text-main-dark mb-4">Recent
                        Submissions</h3>
                    <div
                        class="bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark rounded-card shadow-soft overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-text-sub-light dark:text-text-sub-dark uppercase tracking-wider">
                                            Organization</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-text-sub-light dark:text-text-sub-dark uppercase tracking-wider">
                                            Document</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-text-sub-light dark:text-text-sub-dark uppercase tracking-wider">
                                            Date</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-text-sub-light dark:text-text-sub-dark uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-surface-dark divide-y divide-gray-200 dark:divide-gray-700">
                                    <?php if (empty($recent_submissions)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-text-sub-light">No recent
                                                submissions found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_submissions as $submission): ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div
                                                        class="text-sm font-medium text-text-main-light dark:text-text-main-dark">
                                                        <?= esc($submission['org_name']) ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-text-sub-light dark:text-text-sub-dark">
                                                        <?= esc($submission['document_title']) ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text-sub-light">
                                                    <?= date('M d, Y', strtotime($submission['created_at'])) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('submissionsChart').getContext('2d');
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(44, 62, 80, 0.2)');
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
                        borderWidth: 2,
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
                            bodyColor: '#F8FAFC'
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94A3B8', font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#E2E8F0' },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>