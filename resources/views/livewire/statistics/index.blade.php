<div>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-text">Statistics Dashboard</h1>
            <p class="text-muted">Real-time overview of system performance and trends</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Open Issues -->
            <div class="card hover:border-warning transition-smooth">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-muted">Open Issues</p>
                        <div class="w-10 h-10 rounded-lg bg-warning/20 flex items-center justify-center text-warning">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-text">{{ $kpi['open_issues'] }}</p>
                    <p class="text-xs text-muted mt-1">Currently active</p>
                </div>
            </div>

            <!-- Closed Today -->
            <div class="card hover:border-accent transition-smooth">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-muted">Closed Today</p>
                        <div class="w-10 h-10 rounded-lg bg-accent/20 flex items-center justify-center text-accent">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-text">{{ $kpi['closed_today'] }}</p>
                    <p class="text-xs text-muted mt-1">{{ now()->format('M d, Y') }}</p>
                </div>
            </div>

            <!-- Closed This Week -->
            <div class="card hover:border-primary transition-smooth">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-muted">Closed This Week</p>
                        <div class="w-10 h-10 rounded-lg bg-primary/20 flex items-center justify-center text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-text">{{ $kpi['closed_this_week'] }}</p>
                    <p class="text-xs text-muted mt-1">{{ now()->startOfWeek()->format('M d') }} - {{ now()->endOfWeek()->format('M d') }}</p>
                </div>
            </div>

            <!-- Avg Close Time -->
            <div class="card hover:border-primary transition-smooth">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-muted">Avg. Close Time</p>
                        <div class="w-10 h-10 rounded-lg bg-info/20 flex items-center justify-center text-info">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-text">{{ $kpi['avg_close_time_hours'] }}h</p>
                    <p class="text-xs text-muted mt-1">Average resolution time</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Departments -->
            <div class="card">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold text-text">Top Departments</h3>
                    <a href="{{ route('reports.monthly') }}" class="text-sm text-primary hover:underline">View Reports</a>
                </div>
                <div class="p-6">
                    @if($kpi['top_departments']->count() > 0)
                        <div class="space-y-4">
                            @foreach($kpi['top_departments'] as $index => $dept)
                                @php
                                    $maxCount = $kpi['top_departments']->first()->count ?? 1;
                                @endphp
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-muted w-6">{{ $index + 1 }}</span>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-text">{{ $dept->name }}</span>
                                            <span class="text-sm text-muted">{{ $dept->count }} issues</span>
                                        </div>
                                        <div class="w-full h-2 bg-surface-2 rounded-full overflow-hidden">
                                            <div class="h-full bg-primary rounded-full transition-all" style="width: {{ ($dept->count / $maxCount) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-muted text-center py-8">No data available</p>
                    @endif
                </div>
            </div>

            <!-- Top Issue Types -->
            <div class="card">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold text-text">Top Issue Types</h3>
                    <a href="{{ route('reports.monthly') }}" class="text-sm text-primary hover:underline">View Reports</a>
                </div>
                <div class="p-6">
                    @if($kpi['top_issue_types']->count() > 0)
                        <div class="space-y-4">
                            @foreach($kpi['top_issue_types'] as $index => $type)
                                @php
                                    $maxCount = $kpi['top_issue_types']->first()->count ?? 1;
                                @endphp
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-muted w-6">{{ $index + 1 }}</span>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-text">{{ $type->name }}</span>
                                            <span class="text-sm text-muted">{{ $type->count }} issues</span>
                                        </div>
                                        <div class="w-full h-2 bg-surface-2 rounded-full overflow-hidden">
                                            <div class="h-full bg-accent rounded-full transition-all" style="width: {{ ($type->count / $maxCount) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-muted text-center py-8">No data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Issues Trend (30 days) -->
        @if($kpi['issues_trend']->count() > 0)
            <div class="card mt-6">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="font-semibold text-text">Issues Trend (Last 30 Days)</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-end justify-between gap-1 h-48">
                        @php
                            $maxTrend = $kpi['issues_trend']->max('count') ?? 1;
                        @endphp
                        @foreach($kpi['issues_trend'] as $data)
                            @php
                                $height = max(($data->count / $maxTrend) * 100, 2);
                                $labelDate = \Carbon\Carbon::parse($data->date);
                                $showLabel = $data->date === $kpi['issues_trend']->first()->date ||
                                             $data->date === $kpi['issues_trend']->last()->date ||
                                             $kpi['issues_trend']->count() <= 10;
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1 group">
                                <div class="w-full relative">
                                    <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-medium text-text opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                        {{ $data->count }}
                                    </span>
                                    <div class="w-full bg-primary rounded-t hover:bg-primary/80 transition-smooth" style="height: {{ $height }}%;"></div>
                                </div>
                                @if($showLabel)
                                    <span class="text-xs text-muted">{{ $labelDate->format('M j') }}</span>
                                @else
                                    <span class="text-xs text-transparent">.</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
