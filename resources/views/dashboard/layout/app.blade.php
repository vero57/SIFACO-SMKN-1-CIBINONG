<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="{{ asset('js/custom-alert.js') }}"></script>
    <style>
        * { box-sizing: border-box; }
        html, body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
        .menu-item { transition: all 0.3s ease; }
        .menu-item:hover { transform: translateX(4px); background: rgba(255, 255, 255, 0.1); }
        .menu-item.active { background: rgba(59, 130, 246, 0.2); border-right: 3px solid #3b82f6; }

        /* Sidebar collapse styles */
        #sidebar {
            transition: width 0.3s ease;
        }

        #sidebar.collapsed {
            width: 80px;
        }

        #sidebar.collapsed .sidebar-label {
            display: none;
        }

        .sidebar-label {
            transition: opacity 0.3s ease;
        }
    </style>
    @stack('head')
</head>
<body class="bg-gradient-to-br from-[#F9F9FF] via-slate-50 to-[#F9F9FF] font-sans overflow-hidden">
    <div class="flex h-screen w-screen">
        {{-- Sidebar Overlay for mobile --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar(false)"></div>
        {{-- Sidebar --}}
        <div id="sidebar" class="fixed md:static z-50 md:z-auto top-0 left-0 h-screen w-64 bg-slate-800/50 backdrop-blur-sm border-r border-slate-300 flex flex-col transition-transform duration-300 -translate-x-full md:translate-x-0 overflow-y-auto">
            @include('dashboard.partials.sidebar')
        </div>
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            @include('dashboard.partials.navbar')
            <main class="flex-1 overflow-auto px-2 sm:px-4 md:px-6 py-3 sm:py-4 md:py-6">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        function toggleSidebar(show) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function toggleSidebarDesktop() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('desktopToggleBtn');
            const icon = toggleBtn.querySelector('i');

            sidebar.classList.toggle('collapsed');

            // Toggle icon direction
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        }

        // Restore sidebar state on page load
        window.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('desktopToggleBtn');
            const icon = toggleBtn.querySelector('i');

            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
            }
        });
    </script>
    @stack('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- Alert Component --}}
    @include("components.alert")
</body>
</html>
