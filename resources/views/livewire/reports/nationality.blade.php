<div>
    <style>
        .glass-card {
            background: rgba(var(--surface-1-rgb, 255 255 255), 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(var(--border-rgb, 200 200 200), 0.3);
        }

        .chart-clickable {
            cursor: pointer;
        }
        .chart-clickable:hover {
            opacity: 0.8;
        }
    </style>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Ambient Background -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-0 w-96 h-96 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-accent/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500/20 to-purple-500/5 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-semibold text-text">
                            <span class="bg-gradient-to-r from-purple-500 to-primary bg-clip-text text-transparent">Nationality Statistics</span>
                        </h1>
                    </div>
                    <p class="text-sm text-muted ml-13">Analyze issue data by guest nationality</p>
                </div>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface-2 hover:bg-surface-3 text-sm font-medium text-text transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Reports
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card rounded-xl p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Date Range Preset -->
                <div>
                    <label class="block text-sm font-medium text-text mb-1.5">Date Range</label>
                    <select wire:model.live="dateRangePreset"
                            class="w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                        @foreach($presetOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @if($dateRangePreset === 'custom')
                    <!-- Custom Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">From</label>
                        <input type="date" wire:model.live="dateFrom"
                               class="w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">To</label>
                        <input type="date" wire:model.live="dateTo"
                               class="w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                    </div>
                @endif

                <!-- Department Filter -->
                <div>
                    <label class="block text-sm font-medium text-text mb-1.5">Department</label>
                    <select wire:model.live="selectedDepartmentId"
                            class="w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                        <option value="">All Departments</option>
                        @foreach($availableDepartments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-text mb-1.5">Status</label>
                    <select wire:model.live="selectedStatus"
                            class="w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                        <option value="">All Statuses</option>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <!-- Date Range Summary -->
            <div class="mt-3 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-muted">
                    Showing data from <strong class="text-text">{{ $report['date_from'] }}</strong> to <strong class="text-text">{{ $report['date_to'] }}</strong>
                </span>
            </div>
        </div>

        @if($report)
            <!-- Overview Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Issues -->
                <div class="glass-card rounded-xl p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-muted mb-1">Total Issues</p>
                            <p class="text-3xl font-bold text-text">{{ $report['total_issues'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500/20 to-purple-500/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Nationalities -->
                <div class="glass-card rounded-xl p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-muted mb-1">Unique Nationalities</p>
                            <p class="text-3xl font-bold text-text">{{ $report['total_nationalities'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Top Nationality -->
                <div class="glass-card rounded-xl p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-muted mb-1">Top Nationality</p>
                            <p class="text-lg font-bold text-text truncate max-w-[150px]">{{ $report['top_nationality'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-accent/20 to-accent/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Avg Issues per Nationality -->
                <div class="glass-card rounded-xl p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-muted mb-1">Avg per Nationality</p>
                            <p class="text-3xl font-bold text-text">{{ $report['total_nationalities'] > 0 ? round($report['total_issues'] / $report['total_nationalities'], 1) : 0 }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-success/20 to-success/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="glass-card rounded-xl mb-6">
                <div class="border-b border-border/50">
                    <nav class="flex -mb-px overflow-x-auto">
                        <button wire:click="setActiveTab('distribution')"
                                class="{{ $activeTab === 'distribution' ? 'border-purple-500 text-purple-500' : 'border-transparent text-muted hover:text-text' }} px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                            Distribution
                        </button>
                        <button wire:click="setActiveTab('department')"
                                class="{{ $activeTab === 'department' ? 'border-purple-500 text-purple-500' : 'border-transparent text-muted hover:text-text' }} px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                            By Department
                        </button>
                        <button wire:click="setActiveTab('trends')"
                                class="{{ $activeTab === 'trends' ? 'border-purple-500 text-purple-500' : 'border-transparent text-muted hover:text-text' }} px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                            Trends Over Time
                        </button>
                        <button wire:click="setActiveTab('priority')"
                                class="{{ $activeTab === 'priority' ? 'border-purple-500 text-purple-500' : 'border-transparent text-muted hover:text-text' }} px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                            Priority Analysis
                        </button>
                        <button wire:click="setActiveTab('table')"
                                class="{{ $activeTab === 'table' ? 'border-purple-500 text-purple-500' : 'border-transparent text-muted hover:text-text' }} px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                            Detailed Table
                        </button>
                        <button wire:click="setActiveTab('issue_type')"
                                class="{{ $activeTab === 'issue_type' ? 'border-purple-500 text-purple-500' : 'border-transparent text-muted hover:text-text' }} px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                            By Issue Type
                        </button>
                        <button wire:click="setActiveTab('tree')"
                                class="{{ $activeTab === 'tree' ? 'border-purple-500 text-purple-500' : 'border-transparent text-muted hover:text-text' }} px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                            Tree View
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Distribution Tab -->
                    @if($activeTab === 'distribution')
                        @include('livewire.reports.partials.nationality-distribution')
                    @endif

                    <!-- Department Tab -->
                    @if($activeTab === 'department')
                        @include('livewire.reports.partials.nationality-department')
                    @endif

                    <!-- Trends Tab -->
                    @if($activeTab === 'trends')
                        @include('livewire.reports.partials.nationality-trends')
                    @endif

                    <!-- Priority Tab -->
                    @if($activeTab === 'priority')
                        @include('livewire.reports.partials.nationality-priority')
                    @endif

                    <!-- Table Tab -->
                    @if($activeTab === 'table')
                        @include('livewire.reports.partials.nationality-table')
                    @endif

                    <!-- Issue Type Tab -->
                    @if($activeTab === 'issue_type')
                        @include('livewire.reports.partials.nationality-issue-type')
                    @endif

                    <!-- Tree Tab -->
                    @if($activeTab === 'tree')
                        @include('livewire.reports.partials.nationality-tree')
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
