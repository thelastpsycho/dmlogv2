<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark"
      x-data="{ shortcutsOpen: false }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GuestPulse!') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts - Modern, clean pairing -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Livewire Styles -->
        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen bg-background">
            <!-- Mobile Backdrop -->
            <div x-show="$store.sidebar.isOpen"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-black/50 lg:hidden"
                 @click="$store.sidebar.close()"
                 style="display: none;"></div>

            <!-- Sidebar -->
            <aside x-data="{ closeNav() { this.$store.sidebar.close(); } }" class="fixed inset-y-0 left-0 z-50 w-64 bg-surface border-r border-border lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 transition-transform duration-300 flex flex-col"
                   :class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

                <!-- Logo -->
                <div class="h-14 flex items-center px-4 border-b border-border">
                    <a href="{{ route('dashboard') }}" @click="closeNav" class="flex items-center gap-2.5">
                        <img src="{{ asset('images/logo/guestpulse_horizontal_logo.svg') }}" alt="GuestPulse!" class="h-11 w-auto">
                    </a>
                    <button @click="$store.sidebar.close()" class="ml-auto lg:hidden p-1.5 -mr-1.5 text-muted hover:text-text">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto mobile-nav">
                    @can('issues.view')
                    <div>
                        <a href="{{ route('issues.index') }}"
                           class="nav-link {{ request()->routeIs('issues.*') ? 'active' : '' }}"
                           @click="closeNav">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Issues</span>
                        </a>
                    </div>
                    @endcan

                    @canany(['reports.view', 'reports.monthly', 'reports.yearly', 'reports.logbook'])
                    @can('reports.view')
                    <div>
                        <a href="{{ route('reports.index') }}"
                           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                           @click="closeNav">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Reports</span>
                        </a>
                    </div>
                    @endcan
                    @endcanany

                    @can('graphs.view')
                    <div>
                        <a href="{{ route('graphs.index') }}"
                           class="nav-link {{ request()->routeIs('graphs.*') ? 'active' : '' }}"
                           @click="closeNav">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                            <span>Graphs</span>
                        </a>
                    </div>
                    @endcan

                    @can('statistics.view')
                    <div>
                        <a href="{{ route('statistics.index') }}"
                           class="nav-link {{ request()->routeIs('statistics.*') ? 'active' : '' }}"
                           @click="closeNav">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                            <span>Statistics</span>
                        </a>
                    </div>
                    @endcan

                    @canany(['admin.users.view', 'admin.departments.view', 'admin.issue-types.view', 'admin.roles.view'])
                    <div class="pt-4 border-t border-border">
                        <p class="px-3 mb-2 text-xs font-semibold text-muted uppercase tracking-wide">Admin</p>
                        @can('admin.users.view')
                        <a href="{{ route('admin.users.index') }}"
                           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                           @click="closeNav">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Users</span>
                        </a>
                        @endcan

                        @can('admin.roles.view')
                        <a href="{{ route('admin.roles.index') }}"
                           class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                           @click="closeNav">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Roles</span>
                        </a>
                        @endcan

                        @can('admin.departments.view')
                        <a href="{{ route('admin.departments.index') }}"
                           class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}"
                           @click="closeNav">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Departments</span>
                        </a>
                        @endcan

                        @can('admin.issue-types.view')
                        <a href="{{ route('admin.issue-types.index') }}"
                           class="nav-link {{ request()->routeIs('admin.issue-types.*') ? 'active' : '' }}"
                           @click="closeNav">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span>Issue Types</span>
                        </a>
                        @endcan
                    </div>
                    @endcanany
                </nav>

                <!-- User Section -->
                <div class="p-3 border-t border-border">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xs font-semibold">
                            {{ auth()->user()->name[0] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-text truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-muted capitalize">{{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 min-w-0">
                <!-- Topbar -->
                <header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md border-b border-border">
                    <div class="flex items-center justify-between h-14 px-4 sm:px-6">
                        <!-- Left: Menu Toggle + Breadcrumb -->
                        <div class="flex items-center gap-3">
                            <button @click="$store.sidebar.toggle()" class="lg:hidden text-muted hover:text-text">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            @isset($header)
                                <h1 class="text-base font-semibold text-text">{{ $header }}</h1>
                            @endisset
                        </div>

                        <!-- Right: Search + Actions -->
                        <div class="flex items-center gap-2">
                            <!-- Keyboard Shortcuts Help -->
                            <button @click="shortcutsOpen = true"
                                    class="p-1.5 rounded-lg text-muted hover:text-text hover:bg-surface-2 transition-smooth"
                                    title="Keyboard shortcuts">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>

                            <!-- Theme Toggle -->
                            <button onclick="window.toggleTheme()"
                                    class="p-1.5 rounded-lg text-muted hover:text-text hover:bg-surface-2 transition-smooth"
                                    title="Toggle theme">
                                <svg class="theme-icon-dark w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <svg class="theme-icon-light w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            </button>

                            <!-- Export Open Issues -->
                            @can('issues.export.open')
                            <a href="{{ route('issues.export.open.pdf') }}" target="_blank" class="p-1.5 rounded-lg text-muted hover:text-text hover:bg-surface-2 transition-smooth" title="Export Open Issues">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </a>
                            @endcan

                            <!-- User Menu -->
                            <x-dropdown>
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-2 p-1 rounded-lg hover:bg-surface-2 transition-smooth">
                                        <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xs font-semibold">
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
                <div class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <style>
            .nav-link {
                display: flex;
                align-items: center;
                gap: 0.625rem;
                width: 100%;
                padding: 0.5rem 0.625rem;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                font-weight: 500;
                color: rgb(var(--muted) / 1);
                transition: color 0.15s, background-color 0.15s;
            }
            .nav-link:hover {
                color: rgb(var(--text) / 1);
                background-color: rgb(var(--surface-2) / 1);
            }
            .nav-link.active {
                background-color: rgb(var(--primary) / 1);
                color: rgb(var(--primary-foreground) / 1);
            }
            .nav-link svg {
                flex-shrink: 0;
            }

            /* Custom Scrollbar for dropdowns */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgb(var(--border) / 0.5);
                border-radius: 3px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgb(var(--border) / 0.8);
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
        <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
        <script>
            // Define Alpine stores BEFORE Alpine initializes the DOM
            // This must run synchronously after Livewire loads
            if (typeof Alpine !== 'undefined') {
                // Sidebar store
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

                // Multi-select component function
                window.multiSelect = function(config) {
                    return {
                        selected: config.selected || [],
                        options: config.options || [],
                        isOpen: false,
                        search: '',
                        wireProperty: config.wireProperty || null,

                        init() {
                            if (this.wireProperty) {
                                this.$watch('selected', (val) => {
                                    this.$wire.set(this.wireProperty, val);
                                });
                            }
                        },

                        get filteredOptions() {
                            if (!this.search) return this.options;
                            const search = this.search.toLowerCase();
                            return this.options.filter(opt =>
                                opt.name.toLowerCase().includes(search)
                            );
                        },

                        toggleDropdown() {
                            this.isOpen = !this.isOpen;
                            if (this.isOpen) {
                                this.$nextTick(() => {
                                    const input = this.$el.querySelector('input[type="text"]');
                                    if (input) input.focus();
                                });
                            }
                        },

                        closeDropdown() {
                            this.isOpen = false;
                            this.search = '';
                        },

                        selectAll() {
                            if (this.selected.length === this.filteredOptions.length) {
                                this.selected = [];
                            } else {
                                this.selected = this.filteredOptions.map(opt => opt.id);
                            }
                        }
                    };
                };

                // Custom date picker component
                window.datePicker = function(config) {
                    return {
                        value: config.value || '',
                        isOpen: false,
                        currentDate: new Date(),
                        viewDate: new Date(),
                        selectedMonth: new Date().getMonth(),
                        selectedYear: new Date().getFullYear(),
                        today: new Date(),
                        wireProperty: config.wireProperty || null,

                        get monthNames() {
                            return ['January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'];
                        },

                        get daysInMonth() {
                            return new Date(this.selectedYear, this.selectedMonth + 1, 0).getDate();
                        },

                        get firstDayOfMonth() {
                            return new Date(this.selectedYear, this.selectedMonth, 1).getDay();
                        },

                        get calendarDays() {
                            const days = [];
                            for (let i = 0; i < this.firstDayOfMonth; i++) {
                                days.push(null);
                            }
                            for (let i = 1; i <= this.daysInMonth; i++) {
                                days.push(i);
                            }
                            return days;
                        },

                        get formattedValue() {
                            if (!this.value) return '';
                            const date = new Date(this.value + 'T00:00:00');
                            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                        },

                        open() {
                            this.isOpen = true;
                            if (this.value) {
                                const date = new Date(this.value + 'T00:00:00');
                                this.selectedMonth = date.getMonth();
                                this.selectedYear = date.getFullYear();
                            }
                        },

                        close() {
                            this.isOpen = false;
                        },

                        clear() {
                            this.value = '';
                            // Sync to Livewire
                            if (this.$wire && this.wireProperty) {
                                this.$wire.set(this.wireProperty, '');
                            }
                        },

                        selectDay(day) {
                            const month = String(this.selectedMonth + 1).padStart(2, '0');
                            const dayStr = String(day).padStart(2, '0');
                            this.value = `${this.selectedYear}-${month}-${dayStr}`;
                            // Sync to Livewire
                            if (this.$wire && this.wireProperty) {
                                this.$wire.set(this.wireProperty, this.value);
                            }
                            this.close();
                        },

                        prevMonth() {
                            if (this.selectedMonth === 0) {
                                this.selectedMonth = 11;
                                this.selectedYear--;
                            } else {
                                this.selectedMonth--;
                            }
                        },

                        nextMonth() {
                            if (this.selectedMonth === 11) {
                                this.selectedMonth = 0;
                                this.selectedYear++;
                            } else {
                                this.selectedMonth++;
                            }
                        },

                        isToday(day) {
                            return day === this.today.getDate() &&
                                   this.selectedMonth === this.today.getMonth() &&
                                   this.selectedYear === this.today.getFullYear();
                        },

                        isSelected(day) {
                            if (!this.value) return false;
                            const date = new Date(this.value + 'T00:00:00');
                            return day === date.getDate() &&
                                   this.selectedMonth === date.getMonth() &&
                                   this.selectedYear === date.getFullYear();
                        }
                    };
                };

                // Date range picker component (two months side by side)
                window.dateRangePicker = function(config) {
                    return {
                        startDate: config.startDate || '',
                        endDate: config.endDate || '',
                        isOpen: false,
                        selecting: 'start', // 'start' or 'end'
                        hoveredDate: null,
                        today: new Date(),
                        wirePropertyStart: config.wirePropertyStart || null,
                        wirePropertyEnd: config.wirePropertyEnd || null,
                        viewStartMonth: new Date().getMonth(),
                        viewStartYear: new Date().getFullYear(),

                        get firstMonth() {
                            return new Date(this.viewStartYear, this.viewStartMonth, 1);
                        },

                        get secondMonth() {
                            const nextMonth = new Date(this.viewStartYear, this.viewStartMonth + 1, 1);
                            return nextMonth;
                        },

                        get monthNames() {
                            return ['January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'];
                        },

                        get formattedRange() {
                            if (this.startDate && this.endDate) {
                                const start = new Date(this.startDate + 'T00:00:00');
                                const end = new Date(this.endDate + 'T00:00:00');
                                return `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${end.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`;
                            }
                            if (this.startDate) {
                                const start = new Date(this.startDate + 'T00:00:00');
                                return `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - Select date`;
                            }
                            return 'Select dates';
                        },

                        getDaysInMonth(year, month) {
                            return new Date(year, month + 1, 0).getDate();
                        },

                        getFirstDayOfMonth(year, month) {
                            return new Date(year, month, 1).getDay();
                        },

                        getCalendarDays(year, month) {
                            const days = [];
                            const firstDay = this.getFirstDayOfMonth(year, month);
                            const daysInMonth = this.getDaysInMonth(year, month);
                            for (let i = 0; i < firstDay; i++) {
                                days.push(null);
                            }
                            for (let i = 1; i <= daysInMonth; i++) {
                                days.push(i);
                            }
                            return days;
                        },

                        open() {
                            this.isOpen = true;
                            // Initialize view to show selected dates or current month
                            if (this.startDate) {
                                const start = new Date(this.startDate + 'T00:00:00');
                                this.viewStartMonth = start.getMonth();
                                this.viewStartYear = start.getFullYear();
                            } else {
                                this.viewStartMonth = this.today.getMonth();
                                this.viewStartYear = this.today.getFullYear();
                            }
                        },

                        prevMonths() {
                            // Go back by 2 months to show the previous pair
                            if (this.viewStartMonth === 1) {
                                this.viewStartMonth = 11;
                                this.viewStartYear--;
                            } else if (this.viewStartMonth === 0) {
                                this.viewStartMonth = 10;
                                this.viewStartYear--;
                            } else {
                                this.viewStartMonth -= 2;
                            }
                        },

                        nextMonths() {
                            // Go forward by 2 months to show the next pair
                            if (this.viewStartMonth === 11) {
                                this.viewStartMonth = 1;
                                this.viewStartYear++;
                            } else if (this.viewStartMonth === 10) {
                                this.viewStartMonth = 0;
                                this.viewStartYear++;
                            } else {
                                this.viewStartMonth += 2;
                            }
                        },

                        close() {
                            this.isOpen = false;
                            this.hoveredDate = null;
                        },

                        isToday(year, month, day) {
                            return day === this.today.getDate() &&
                                   month === this.today.getMonth() &&
                                   year === this.today.getFullYear();
                        },

                        isSelected(year, month, day) {
                            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                            if (this.startDate && dateStr === this.startDate) return 'start';
                            if (this.endDate && dateStr === this.endDate) return 'end';
                            return false;
                        },

                        isInRange(year, month, day) {
                            if (!this.startDate || !this.endDate) return false;
                            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                            return dateStr > this.startDate && dateStr < this.endDate;
                        },

                        isHovered(year, month, day) {
                            if (!this.hoveredDate) return false;
                            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                            return dateStr === this.hoveredDate;
                        },

                        isInRangeOrHovered(year, month, day) {
                            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                            if (this.startDate && this.endDate) {
                                return dateStr > this.startDate && dateStr < this.endDate;
                            }
                            if (this.startDate && this.hoveredDate) {
                                const start = this.startDate < this.hoveredDate ? this.startDate : this.hoveredDate;
                                const end = this.startDate > this.hoveredDate ? this.startDate : this.hoveredDate;
                                return dateStr >= start && dateStr <= end;
                            }
                            return false;
                        },

                        selectDate(year, month, day) {
                            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                            // If we have both dates, or no dates, start fresh
                            if (!this.startDate || (this.startDate && this.endDate)) {
                                this.startDate = dateStr;
                                this.endDate = '';
                                this.selecting = 'end';
                                // Sync to Livewire
                                if (this.wirePropertyStart) this.$wire.set(this.wirePropertyStart, this.startDate);
                                if (this.wirePropertyEnd) this.$wire.set(this.wirePropertyEnd, '');
                            } else {
                                // We have startDate, need to set endDate
                                if (dateStr < this.startDate) {
                                    // User clicked a date before startDate - swap them
                                    this.endDate = this.startDate;
                                    this.startDate = dateStr;
                                } else {
                                    this.endDate = dateStr;
                                }
                                this.selecting = 'complete';
                                // Sync to Livewire
                                if (this.wirePropertyStart) this.$wire.set(this.wirePropertyStart, this.startDate);
                                if (this.wirePropertyEnd) this.$wire.set(this.wirePropertyEnd, this.endDate);
                            }
                        },

                        setHovered(year, month, day) {
                            this.hoveredDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        },

                        clearHovered() {
                            this.hoveredDate = null;
                        },

                        clear() {
                            this.startDate = '';
                            this.endDate = '';
                            this.selecting = 'start';
                            // Sync to Livewire
                            if (this.wirePropertyStart) this.$wire.set(this.wirePropertyStart, '');
                            if (this.wirePropertyEnd) this.$wire.set(this.wirePropertyEnd, '');
                        }
                    };
                };

                // Nationality select component (single select with search)
                window.nationalitySelect = function() {
                    return {
                        selected: '',
                        options: [],
                        isOpen: false,
                        search: '',
                        initSelect(value) {
                            this.selected = value || '';
                            this.$nextTick(() => {
                                const dataEl = this.$el.querySelector('[data-nationalities]');
                                if (dataEl) {
                                    this.options = dataEl.getAttribute('data-nationalities').split(',');
                                }
                            });
                        },
                        get filteredOptions() {
                            if (!this.search) return this.options;
                            return this.options.filter(opt =>
                                opt.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        select(option) {
                            this.selected = option;
                            this.$wire.set('nationality', option);
                            this.closeDropdown();
                        },
                        clear() {
                            this.selected = '';
                            this.$wire.set('nationality', '');
                        },
                        toggleDropdown() {
                            this.isOpen = !this.isOpen;
                            // Ensure options are loaded when opening
                            if (this.isOpen && this.options.length === 0) {
                                const dataEl = this.$el.querySelector('[data-nationalities]');
                                if (dataEl) {
                                    this.options = dataEl.getAttribute('data-nationalities').split(',');
                                }
                            }
                        },
                        closeDropdown() {
                            this.isOpen = false;
                            this.search = '';
                        }
                    };
                };
            }
        </script>
        <script>
            // Global theme toggle function
            window.toggleTheme = function() {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.classList.add('light');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.remove('light');
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                // Update icons visibility using Tailwind's hidden class
                const darkIcons = document.querySelectorAll('.theme-icon-dark');
                const lightIcons = document.querySelectorAll('.theme-icon-light');
                const shouldBeDark = document.documentElement.classList.contains('dark');
                darkIcons.forEach(icon => icon.classList.toggle('hidden', !shouldBeDark));
                lightIcons.forEach(icon => icon.classList.toggle('hidden', shouldBeDark));
            };
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
