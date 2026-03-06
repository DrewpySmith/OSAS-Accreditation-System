<?php
$currentUri = current_url(true)->getPath(); // e.g. /index.php/admin/organizations
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
        <?= $title ?? 'Admin Dashboard' ?> - USG Accreditation
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

        .content-transition {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

        .sidebar-collapsed .team-selector {
            display: none !important;
        }
    </style>
    <script>
        // Apply collapsed state immediately to prevent flash
        (function () {
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
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
                    class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3 shadow-lg flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 01.75 12c0 6.213 4.963 11.25 11.25 11.25s11.25-5.037 11.25-11.25c0-2.396-.754-4.62-2.046-6.448A11.963 11.963 0 0112 2.714z" />
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

            <!-- Team selector -->
            <div class="px-4 py-4 team-selector">
                <div class="w-full flex items-center justify-between px-3 py-2 bg-muted rounded-md">
                    <div class="flex items-center">
                        <div
                            class="w-6 h-6 rounded-md bg-blue-600 mr-2 flex items-center justify-center text-xs text-white font-bold">
                            A</div>
                        <span class="text-sm font-medium">Admin Portal</span>
                    </div>
                    <span class="text-xs text-muted-foreground uppercase">⌘ K</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto custom-scrollbar">
                <p class="px-3 mb-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider section-label">
                    Overview</p>
                <div class="space-y-1">
                    <a href="<?= base_url('admin/dashboard') ?>"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('admin/dashboard') ? 'bg-blue-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
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
                    Management</p>
                <div class="space-y-1">
                    <a href="<?= base_url('admin/organizations') ?>"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('admin/organizations') ? 'bg-blue-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Organizations">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 nav-icon" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="sidebar-text text-truncate">Organizations</span>
                    </a>
                    <a href="<?= base_url('admin/documents') ?>"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('admin/documents') ? 'bg-blue-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Review Documents">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 nav-icon" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="sidebar-text text-truncate">Review Documents</span>
                    </a>
                    <a href="<?= base_url('admin/statistics') ?>"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all nav-item <?= $isActive('admin/statistics') ? 'bg-blue-600 text-white' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                        title="Statistics">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 flex-shrink-0 nav-icon" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="sidebar-text text-truncate">Statistics</span>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted-foreground" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    <span class="text-sm font-medium text-muted-foreground">
                        <?= $title ?? 'Admin' ?>
                    </span>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle"
                        class="p-2 rounded-md hover:bg-muted text-muted-foreground transition-colors"
                        title="Toggle Theme">
                        <svg id="theme-icon-dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg id="theme-icon-light" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <span class="text-sm text-muted-foreground hidden sm:inline-block">Welcome, <span
                            class="font-medium text-foreground"><?= session()->get('username') ?></span></span>
                    <div
                        class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                        <?= strtoupper(substr(session()->get('username') ?? 'A', 0, 1)) ?>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 sm:p-8 bg-muted/20">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <div id="react-modals"></div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Theme Management
        function applyTheme(theme) {
            const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);

            const darkIcon = document.getElementById('theme-icon-dark');
            const lightIcon = document.getElementById('theme-icon-light');
            if (darkIcon && lightIcon) {
                if (isDark) {
                    darkIcon.classList.remove('hidden');
                    lightIcon.classList.add('hidden');
                } else {
                    lightIcon.classList.remove('hidden');
                    darkIcon.classList.add('hidden');
                }
            }
        }

        const savedTheme = localStorage.getItem('theme') || 'dark'; // Default to dark as requested previously
        applyTheme(savedTheme);

        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();

            // Sidebar Toggle
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';

            if (isCollapsed) {
                sidebar.classList.add('sidebar-collapsed');
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    const newState = sidebar.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', newState);

                    if (newState) {
                        document.documentElement.classList.add('sidebar-is-collapsed');
                    } else {
                        document.documentElement.classList.remove('sidebar-is-collapsed');
                    }
                });
            }

            // Theme Toggle click handler
            const themeToggleBtn = document.getElementById('theme-toggle');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const currentIsDark = document.documentElement.classList.contains('dark');
                    const newTheme = currentIsDark ? 'light' : 'dark';
                    localStorage.setItem('theme', newTheme);
                    applyTheme(newTheme);
                });
            }
        });
    </script>
</body>

</html>