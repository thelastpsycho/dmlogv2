<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark"
      x-data="{ shortcutsOpen: false }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DM-Log') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Livewire Styles -->
        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen bg-background">
            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-surface border-r border-border transition-transform duration-300 lg:translate-x-0"
                   :class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-border">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-primary" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                        <span class="text-xl font-bold text-text">DM-Log</span>
                    </a>
                    <button @click="$store.sidebar.close()" class="lg:hidden text-muted hover:text-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <!-- Issues -->
                    @can('issues.view')
                    <div class="space-y-1">
                        <p class="px-2 text-xs font-semibold text-muted uppercase tracking-wider">Issues</p>
                        <a href="{{ route('issues.index') }}"
                           class="nav-link {{ request()->routeIs('issues.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>All Issues</span>
                        </a>
                    </div>
                    @endcan

                    <!-- Reports -->
                    @canany(['reports.view', 'reports.monthly', 'reports.yearly', 'reports.logbook'])
                    <div class="mt-6 space-y-1">
                        <p class="px-2 text-xs font-semibold text-muted uppercase tracking-wider">Reports</p>
                        @can('reports.view')
                        <a href="{{ route('reports.index') }}"
                           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Reports</span>
                        </a>
                        @endcan
                    </div>
                    @endcanany

                    <!-- Insights -->
                    @canany(['graphs.view', 'statistics.view'])
                    <div class="mt-6 space-y-1">
                        <p class="px-2 text-xs font-semibold text-muted uppercase tracking-wider">Insights</p>
                        @can('graphs.view')
                        <a href="{{ route('graphs.index') }}"
                           class="nav-link {{ request()->routeIs('graphs.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                            <span>Graphs</span>
                        </a>
                        @endcan
                        @can('statistics.view')
                        <a href="{{ route('statistics.index') }}"
                           class="nav-link {{ request()->routeIs('statistics.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                            <span>Statistics</span>
                        </a>
                        @endcan
                    </div>
                    @endcanany

                    <!-- Admin -->
                    @canany(['admin.users.view', 'admin.departments.view', 'admin.issue-types.view', 'admin.roles.view'])
                    <div class="mt-6 space-y-1">
                        <p class="px-2 text-xs font-semibold text-muted uppercase tracking-wider">Admin</p>
                        @can('admin.users.view')
                        <a href="{{ route('admin.users.index') }}"
                           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Users</span>
                        </a>
                        @endcan
                        @can('admin.departments.view')
                        <a href="{{ route('admin.departments.index') }}"
                           class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Departments</span>
                        </a>
                        @endcan
                        @can('admin.issue-types.view')
                        <a href="{{ route('admin.issue-types.index') }}"
                           class="nav-link {{ request()->routeIs('admin.issue-types.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span>Issue Types</span>
                        </a>
                        @endcan
                    </div>
                    @endcanany
                </nav>

                <!-- User Section -->
                <div class="p-4 border-t border-border">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-semibold">
                            {{ auth()->user()->name[0] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-text truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-muted truncate">{{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 lg:ml-64">
                <!-- Topbar -->
                <header class="sticky top-0 z-40 bg-surface/80 backdrop-blur-md border-b border-border">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                        <!-- Left: Menu Toggle + Breadcrumb -->
                        <div class="flex items-center gap-4">
                            <button @click="$store.sidebar.toggle()" class="lg:hidden text-muted hover:text-text">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            @isset($header)
                                <h1 class="text-lg font-semibold text-text">{{ $header }}</h1>
                            @endisset
                        </div>

                        <!-- Right: Search + Actions -->
                        <div class="flex items-center gap-3">
                            <!-- Keyboard Shortcuts Help -->
                            <button @click="shortcutsOpen = true"
                                    class="p-2 rounded-lg text-muted hover:text-text hover:bg-surface-2 transition-smooth"
                                    title="Keyboard shortcuts">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>

                            <!-- Theme Toggle -->
                            <button @click="$store.theme.toggle()"
                                    class="p-2 rounded-lg text-muted hover:text-text hover:bg-surface-2 transition-smooth"
                                    title="Toggle theme">
                                <svg x-show="$store.theme.isDark()" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <svg x-show="!$store.theme.isDark()" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            </button>

                            <!-- User Menu -->
                            <x-dropdown>
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-2 p-1 rounded-lg hover:bg-surface-2 transition-smooth">
                                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary text-sm font-semibold">
                                            {{ auth()->user()->name[0] }}
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('profile.index') }}">
                                        Profile
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Log Out
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                        @csrf
                                    </form>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <style>
            .nav-link {
                @apply flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-muted hover:text-text hover:bg-surface-2 transition-smooth;
            }
            .nav-link.active {
                @apply bg-primary/20 text-primary;
            }
        </style>

        <!-- Keyboard Shortcuts Modal -->
        <div x-show="shortcutsOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[100] flex items-center justify-center p-4"
             @keydown.escape="shortcutsOpen = false"
             style="display: none;">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="shortcutsOpen = false"></div>

            <!-- Modal -->
            <div class="relative bg-surface border border-border rounded-xl shadow-2xl w-full max-w-lg overflow-hidden"
                 @click.away="shortcutsOpen = false">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                    <h2 class="text-lg font-semibold text-text">Keyboard Shortcuts</h2>
                    <button @click="shortcutsOpen = false" class="text-muted hover:text-text">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text">Go to Issues</span>
                        <kbd class="px-2 py-1 text-xs font-mono text-muted bg-surface-2 border border-border rounded">G</kbd>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text">Go to Reports</span>
                        <kbd class="px-2 py-1 text-xs font-mono text-muted bg-surface-2 border border-border rounded">R</kbd>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text">Go to Statistics</span>
                        <kbd class="px-2 py-1 text-xs font-mono text-muted bg-surface-2 border border-border rounded">S</kbd>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text">Go to Dashboard</span>
                        <kbd class="px-2 py-1 text-xs font-mono text-muted bg-surface-2 border border-border rounded">D</kbd>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text">Create New Issue</span>
                        <kbd class="px-2 py-1 text-xs font-mono text-muted bg-surface-2 border border-border rounded">C</kbd>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text">Focus Search</span>
                        <kbd class="px-2 py-1 text-xs font-mono text-muted bg-surface-2 border border-border rounded">/</kbd>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text">Toggle Shortcuts Help</span>
                        <kbd class="px-2 py-1 text-xs font-mono text-muted bg-surface-2 border border-border rounded">?</kbd>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-border bg-surface-2">
                    <p class="text-xs text-muted">Press <kbd class="px-1 py-0.5 text-xs font-mono bg-background border border-border rounded">Esc</kbd> to close</p>
                </div>
            </div>
        </div>

        <!-- Livewire Scripts -->
        <script src="{{ asset('vendor/livewire/livewire.js') }}" defer></script>

        <!-- Alpine.js Stores (must be defined before Alpine initializes) -->
        <script>
            // Use Alpine's init event to define stores before it processes the DOM
            document.addEventListener('alpine:init', () => {
                // Theme management store
                Alpine.store('theme', {
                    toggle() {
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            document.documentElement.classList.add('light');
                            localStorage.setItem('theme', 'light');
                        } else {
                            document.documentElement.classList.remove('light');
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('theme', 'dark');
                        }
                    },
                    isDark() {
                        return document.documentElement.classList.contains('dark');
                    }
                });

                // Sidebar state store
                Alpine.store('sidebar', {
                    isOpen: false,
                    toggle() {
                        this.isOpen = !this.isOpen;
                    },
                    close() {
                        this.isOpen = false;
                    },
                    open() {
                        this.isOpen = true;
                    }
                });
            });
        </script>

        <!-- Keyboard Shortcuts Script -->
        <script>
            document.addEventListener('keydown', (e) => {
                // Ignore if user is typing in an input
                if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
                    return;
                }

                const key = e.key.toLowerCase();

                switch(key) {
                    case 'g':
                        window.location.href = '{{ route('issues.index') }}';
                        break;
                    case 'r':
                        window.location.href = '{{ route('reports.index') }}';
                        break;
                    case 's':
                        window.location.href = '{{ route('statistics.index') }}';
                        break;
                    case 'd':
                        window.location.href = '{{ route('dashboard') }}';
                        break;
                    case 'c':
                        @can('issues.create')
                            window.location.href = '{{ route('issues.create') }}';
                        @endcan
                        break;
                    case '/':
                        e.preventDefault();
                        const searchInput = document.querySelector('input[placeholder*="Search"]');
                        if (searchInput) {
                            searchInput.focus();
                            searchInput.select();
                        }
                        break;
                }
            });
        </script>
    </body>
</html>
