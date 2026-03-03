<div>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-text">Issues Graphs</h1>
                <p class="text-muted">Visual trends and breakdowns</p>
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
                </div>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="card mb-6">
            <div class="px-6 py-4 border-b border-border">
                <h3 class="font-semibold text-text">Monthly Trend</h3>
                <p class="text-sm text-muted">Issues created per month</p>
            </div>
            <div class="p-6">
                <div class="flex items-end justify-between gap-2 h-64">
                    @php
                        $maxMonthly = collect($chartData['monthly'])->max('count') ?? 1;
                    @endphp
                    @foreach($chartData['monthly'] as $data)
                        <div class="flex-1 flex flex-col items-center gap-2 group">
                            <span class="text-sm font-medium text-text opacity-0 group-hover:opacity-100 transition-opacity">{{ $data['count'] }}</span>
                            <div class="w-full bg-primary rounded-t hover:bg-primary/80 transition-smooth cursor-default relative" style="height: {{ max(($data['count'] / $maxMonthly) * 100, 2) }}%;">
                                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-surface-1 border border-border rounded px-2 py-1 text-xs opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                    {{ $data['label'] }}: {{ $data['count'] }} issues
                                </div>
                            </div>
                            <span class="text-xs text-muted">{{ $data['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Status Breakdown -->
            <div class="card">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="font-semibold text-text">Status Breakdown</h3>
                    <p class="text-sm text-muted">Open vs Closed</p>
                </div>
                <div class="p-6">
                    @php
                        $totalStatus = $chartData['status']['open'] + $chartData['status']['closed'];
                        $openPercent = $totalStatus > 0 ? round(($chartData['status']['open'] / $totalStatus) * 100) : 0;
                        $closedPercent = $totalStatus > 0 ? round(($chartData['status']['closed'] / $totalStatus) * 100) : 0;
                    @endphp
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-text">Open</span>
                                <span class="text-sm text-muted">{{ $chartData['status']['open'] }} ({{ $openPercent }}%)</span>
                            </div>
                            <div class="w-full h-4 bg-surface-2 rounded-full overflow-hidden">
                                <div class="h-full bg-warning rounded-full transition-all" style="width: {{ $openPercent }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-text">Closed</span>
                                <span class="text-sm text-muted">{{ $chartData['status']['closed'] }} ({{ $closedPercent }}%)</span>
                            </div>
                            <div class="w-full h-4 bg-surface-2 rounded-full overflow-hidden">
                                <div class="h-full bg-accent rounded-full transition-all" style="width: {{ $closedPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Departments Breakdown -->
            @if(isset($chartData['departments']) && count($chartData['departments']) > 0)
                <div class="card">
                    <div class="px-6 py-4 border-b border-border">
                        <h3 class="font-semibold text-text">Top Departments</h3>
                        <p class="text-sm text-muted">By issue count</p>
                    </div>
                    <div class="p-6">
                        @php
                            $maxDept = collect($chartData['departments'])->max('count') ?? 1;
                        @endphp
                        <div class="space-y-3">
                            @foreach($chartData['departments'] as $dept)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm text-text">{{ $dept['name'] }}</span>
                                        <span class="text-sm text-muted">{{ $dept['count'] }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-surface-2 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary rounded-full" style="width: {{ ($dept['count'] / $maxDept) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Issue Types Breakdown -->
            @if(isset($chartData['issueTypes']) && count($chartData['issueTypes']) > 0)
                <div class="card">
                    <div class="px-6 py-4 border-b border-border">
                        <h3 class="font-semibold text-text">Top Issue Types</h3>
                        <p class="text-sm text-muted">By issue count</p>
                    </div>
                    <div class="p-6">
                        @php
                            $maxType = collect($chartData['issueTypes'])->max('count') ?? 1;
                        @endphp
                        <div class="space-y-3">
                            @foreach($chartData['issueTypes'] as $type)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm text-text">{{ $type['name'] }}</span>
                                        <span class="text-sm text-muted">{{ $type['count'] }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-surface-2 rounded-full overflow-hidden">
                                        <div class="h-full bg-accent rounded-full" style="width: {{ ($type['count'] / $maxType) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
