<?php
$currentUri = current_url(true)->getPath();
$isActive = function (string $segment) use ($currentUri): bool {
    return str_contains($currentUri, $segment);
};
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        <?= $title ?? 'Organization Dashboard' ?> - USG Accreditation
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <meta name="<?= csrf_header() ?>" content="<?= csrf_hash() ?>">
    <?= helper('vite');
    echo vite_assets(['resources/js/main.jsx', 'resources/css/app.css']); ?>
    <style>
        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed {
            width: 4.5rem !important;
        }

        .sidebar-collapsed .sidebar-text {
            display: none !important;
        }

        .sidebar-collapsed .sidebar-header-logo {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar-collapsed .nav-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar-collapsed .nav-icon {
            margin-right: 0 !important;
        }

        .sidebar-collapsed .section-label {
            display: none !important;
        }

        .sidebar-collapsed .quick-upload-wrapper {
            display: none !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: white !important;
                color: black !important;
                height: auto !important;
                overflow: visible !important;
            }

            .flex.h-screen {
                display: block !important;
                height: auto !important;
            }

            aside,
            header {
                display: none !important;
            }

            main {
                display: block !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }

            .p-8 {
                padding: 0 !important;
            }

            .bg-card {
                background-color: white !important;
                box-shadow: none !important;
                border: none !important;
            }

            .border {
                border-color: #000 !important;
            }

            .text-muted-foreground {
                color: #000 !important;
            }

            .text-green-400,
            .text-blue-400,
            .text-red-400 {
                color: black !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
            }

            .bg-muted\/50 {
                background-color: #fff !important;
            }
        }
    </style>
    <script>
        // Apply collapsed state immediately to prevent flash
        (function () {
            const isCollapsed = localStorage.getItem('sidebar-collapsed-org') === 'true';
            if (isCollapsed) {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        })();
    </script>
</head>

<body class="bg-background text-foreground font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-64 flex flex-col border-r bg-card text-card-foreground flex-shrink-0 sidebar-transition">
            <!-- Logo & Toggle -->
            <div class="h-16 flex items-center px-6 border-b sidebar-header-logo relative">
                <div
                    class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                </div>
                <span class="font-semibold text-lg tracking-tight sidebar-text">Accredify</span>
                <button id="sidebar-toggle"
                    class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-card border rounded-full flex items-center justify-center shadow-sm hover:bg-muted transition-colors z-20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted-foreground rotate-toggle"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Quick Upload -->
            <div class="px-4 py-4 quick-upload-wrapper">
                <button id="quick-upload-btn"
                    class="w-full flex items-center justify-between px-3 py-2 bg-green-600/10 border border-green-500/20 hover:bg-green-600/20 rounded-md transition-colors text-green-400 font-semibold">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-sm">Quick Upload</span>
                    </div>
                    <span class="text-xs text-muted-foreground opacity-50 uppercase">⌘ U</span>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto custom-scrollbar">
                <p class="px-3 mb-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider section-label">
                    Overview</p>
                <div class="space-y-1">
                    <a href="<?= base_url('organization/dashboard') ?>"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('organization/dashboard') ? 'bg-green-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 nav-icon" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="sidebar-text text-truncate">Dashboard</span>
                    </a>
                </div>

                <p
                    class="px-3 mt-6 mb-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider section-label">
                    Accreditation</p>
                <div class="space-y-1">
                    <a href="<?= base_url('organization/submissions') ?>"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('organization/submissions') ? 'bg-green-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="My Submissions">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 nav-icon" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="sidebar-text text-truncate">My Submissions</span>
                    </a>
                    <a href="<?= base_url('organization/accreditation') ?>"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('organization/accreditation') ? 'bg-green-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Accreditation">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 nav-icon" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                        </svg>
                        <span class="sidebar-text text-truncate">Accreditation</span>
                    </a>
                    <a href="<?= base_url('organization/notifications') ?>"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('organization/notifications') ? 'bg-green-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 nav-icon" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="sidebar-text text-truncate">Notifications</span>
                        <?php if (isset($unread_count) && $unread_count > 0): ?>
                            <span
                                class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold sidebar-text mr-1">
                                <?= $unread_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>

                <p
                    class="px-3 mt-6 mb-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider section-label">
                    Management</p>
                <div class="space-y-1">
                    <a href="<?= base_url('organization/commitment-form') ?>"
                        class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('commitment-form') ? 'bg-green-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Commitment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 opacity-70 nav-icon"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span class="sidebar-text text-truncate">Commitment</span>
                    </a>
                    <a href="<?= base_url('organization/calendar-activities') ?>"
                        class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('calendar-activities') ? 'bg-green-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Calendar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 opacity-70 nav-icon"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="sidebar-text text-truncate">Calendar</span>
                    </a>
                    <a href="<?= base_url('organization/financial-report') ?>"
                        class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-all nav-item <?= ($isActive('financial-report') && !$isActive('financial-report/tracking')) ? 'bg-green-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Financials">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 opacity-70 nav-icon"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="sidebar-text text-truncate">Financials</span>
                    </a>
                    <a href="<?= base_url('organization/financial-report/tracking') ?>"
                        class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('financial-report/tracking') ? 'bg-green-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Tracking">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 opacity-70 nav-icon"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="sidebar-text text-truncate">Tracking</span>
                    </a>
                </div>
            </nav>

            <!-- Footer / Logout -->
            <div class="p-4 border-t">
                <a href="<?= base_url('logout') ?>"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-muted-foreground hover:bg-red-500/10 hover:text-red-400 transition-all nav-item"
                    title="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 nav-icon" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="sidebar-text text-truncate">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden relative">
            <header class="h-14 flex items-center justify-between px-6 border-b bg-card z-10">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-sm font-medium"><?= esc($organization['name'] ?? 'Organization') ?></span>
                </div>
                <div class="flex items-center space-x-3">
                    <span
                        class="text-xs text-muted-foreground bg-green-600/10 border border-green-500/20 text-green-400 px-2 py-0.5 rounded-full font-medium">
                        A.Y. <?= $academic_year ?? date('Y') ?>
                    </span>
                    <div
                        class="h-8 w-8 rounded-full bg-green-600 flex items-center justify-center text-white text-xs font-bold">
                        <?= strtoupper(substr(session()->get('username') ?? 'O', 0, 1)) ?>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <!-- Container for React Modals (UploadModal) -->
    <div id="react-modals"></div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const isCollapsed = localStorage.getItem('sidebar-collapsed-org') === 'true';

            if (isCollapsed) {
                sidebar.classList.add('sidebar-collapsed');
            }

            toggle.addEventListener('click', function () {
                const newState = sidebar.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed-org', newState);

                if (newState) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                } else {
                    document.documentElement.classList.remove('sidebar-is-collapsed');
                }
            });
        });
    </script>
</body>

</html>