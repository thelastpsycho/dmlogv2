<div>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-text">Monthly Report</h1>
                <p class="text-muted">Detailed breakdown of issues for a specific month</p>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-6">
            <div class="p-4">
                <div class="flex flex-wrap gap-4 items-end">
                    <div>
                        <x-input-label for="year" value="Year" />
                        <select id="year" wire:model.live="selectedYear" class="mt-1 bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                            @foreach($availableYears as $year => $label)
                                <option value="{{ $year }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="month" value="Month" />
                        <select id="month" wire:model.live="selectedMonth" class="mt-1 bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                            @foreach($availableMonths as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if($report)
            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="card">
                    <div class="p-6">
                        <p class="text-sm text-muted mb-1">Total Issues</p>
                        <p class="text-3xl font-bold text-text">{{ $report['total_issues'] }}</p>
                    </div>
                </div>
                <div class="card">
                    <div class="p-6">
                        <p class="text-sm text-muted mb-1">Open Issues</p>
                        <p class="text-3xl font-bold text-warning">{{ $report['by_status']['open'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="card">
                    <div class="p-6">
                        <p class="text-sm text-muted mb-1">Closed Issues</p>
                        <p class="text-3xl font-bold text-accent">{{ $report['by_status']['closed'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="card">
                    <div class="p-6">
                        <p class="text-sm text-muted mb-1">Avg. Close Time</p>
                        <p class="text-3xl font-bold text-primary">{{ $report['avg_close_time_hours'] }}h</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- By Department -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-border">
                        <h3 class="font-semibold text-text">By Department</h3>
                    </div>
                    <div class="p-6">
                        @if($report['by_department']->count() > 0)
                            <div class="space-y-3">
                                @foreach($report['by_department'] as $department => $count)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-text">{{ $department }}</span>
                                        <div class="flex items-center gap-2">
                                            <div class="w-32 h-2 bg-surface-2 rounded-full overflow-hidden">
                                                <div class="h-full bg-primary rounded-full" style="width: {{ ($count / $report['total_issues']) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-text w-8 text-right">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-muted">No data available</p>
                        @endif
                    </div>
                </div>

                <!-- By Issue Type -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-border">
                        <h3 class="font-semibold text-text">By Issue Type</h3>
                    </div>
                    <div class="p-6">
                        @if($report['by_issue_type']->count() > 0)
                            <div class="space-y-3">
                                @foreach($report['by_issue_type'] as $type => $count)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-text">{{ $type }}</span>
                                        <div class="flex items-center gap-2">
                                            <div class="w-32 h-2 bg-surface-2 rounded-full overflow-hidden">
                                                <div class="h-full bg-accent rounded-full" style="width: {{ ($count / $report['total_issues']) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-text w-8 text-right">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-muted">No data available</p>
                        @endif
                    </div>
                </div>

                <!-- By Severity -->
                @if($report['by_severity']->count() > 0)
                    <div class="card">
                        <div class="px-6 py-4 border-b border-border">
                            <h3 class="font-semibold text-text">By Severity</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($report['by_severity'] as $severity => $count)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-text capitalize">{{ $severity }}</span>
                                        <div class="flex items-center gap-2">
                                            <div class="w-32 h-2 bg-surface-2 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $severity === 'urgent' ? 'bg-danger' : ($severity === 'high' ? 'bg-warning' : 'bg-muted') }}" style="width: {{ ($count / $report['total_issues']) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-text w-8 text-right">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status Distribution -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-border">
                        <h3 class="font-semibold text-text">Status Distribution</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-text">Open</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 h-2 bg-surface-2 rounded-full overflow-hidden">
                                        <div class="h-full bg-warning rounded-full" style="width: {{ (($report['by_status']['open'] ?? 0) / max($report['total_issues'], 1)) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-text w-8 text-right">{{ $report['by_status']['open'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-text">Closed</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 h-2 bg-surface-2 rounded-full overflow-hidden">
                                        <div class="h-full bg-accent rounded-full" style="width: {{ (($report['by_status']['closed'] ?? 0) / max($report['total_issues'], 1)) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-text w-8 text-right">{{ $report['by_status']['closed'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
