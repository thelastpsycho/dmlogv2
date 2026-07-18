<!-- Distribution Charts -->
<div class="space-y-6">
    @if($report['by_nationality']->count() > 0)
        <!-- Horizontal Bar Chart -->
        <div>
            <h3 class="text-lg font-medium text-text mb-4">Issues by Nationality (Top 15)</h3>
            @php
                $topNationalities = $report['by_nationality']->take(15);
                $maxCount = $topNationalities->max(function($item) { return $item['count']; }) ?? 1;
                $colors = [
                    'from-purple-500 to-purple-600',
                    'from-primary to-primary/80',
                    'from-accent to-accent/80',
                    'from-success to-success/80',
                    'from-warning to-warning/80',
                    'from-danger to-danger/80',
                ];
            @endphp

            <div class="space-y-3">
                @foreach($topNationalities as $nationality => $data)
                    @php
                        $colorIndex = ($loop->iteration - 1) % count($colors);
                        $colorClass = $colors[$colorIndex];
                    @endphp
                    <div class="group">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-text group-hover:text-purple-500 transition-colors">{{ $nationality }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-muted">{{ $data['percentage'] }}%</span>
                                <span class="text-sm font-bold text-text">{{ $data['count'] }}</span>
                            </div>
                        </div>
                        <div class="h-3 bg-surface-2 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r {{ $colorClass }} rounded-full transition-all duration-500 group-hover:opacity-80"
                                 style="width: {{ ($data['count'] / $maxCount) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pie Chart (CSS-based) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-text mb-4">Distribution</h3>
                @php
                    $totalForChart = $report['by_nationality']->sum(function($item) { return $item['count']; });
                    $topForPie = $report['by_nationality']->take(5);
                    $othersCount = $report['by_nationality']->slice(5)->sum(function($item) { return $item['count']; });
                @endphp

                <div class="flex items-center justify-center">
                    <div class="relative w-64 h-64">
                        <svg viewBox="0 0 36 36" class="w-full h-full">
                            @php
                                $cumulativePercent = 0;
                                $pieColors = [
                                    '#a855f7', // purple-500
                                    '#3b82f6', // primary
                                    '#06b6d4', // accent
                                    '#22c55e', // success
                                    '#f59e0b', // warning
                                    '#94a3b8', // muted (others)
                                ];
                            @endphp

                            @foreach($topForPie as $nationality => $data)
                                @php
                                    $percent = ($data['count'] / $totalForChart) * 100;
                                    $dashArray = $percent . ' ' . (100 - $percent);
                                    $offset = 25 - $cumulativePercent;
                                    $color = $pieColors[$loop->iteration - 1] ?? '#94a3b8';
                                @endphp
                                <circle cx="18" cy="18" r="15.91549430918954"
                                        fill="transparent"
                                        stroke="{{ $color }}"
                                        stroke-width="3"
                                        stroke-dasharray="{{ $dashArray }}"
                                        stroke-dashoffset="{{ $offset }}"
                                        class="transition-all duration-300 hover:opacity-80"
                                        title="{{ $nationality }}: {{ $data['percentage'] }}%"/>
                                @php
                                    $cumulativePercent += $percent;
                                @endphp
                            @endforeach

                            @if($othersCount > 0)
                                @php
                                    $percent = ($othersCount / $totalForChart) * 100;
                                    $dashArray = $percent . ' ' . (100 - $percent);
                                    $offset = 25 - $cumulativePercent;
                                @endphp
                                <circle cx="18" cy="18" r="15.91549430918954"
                                        fill="transparent"
                                        stroke="#94a3b8"
                                        stroke-width="3"
                                        stroke-dasharray="{{ $dashArray }}"
                                        stroke-dashoffset="{{ $offset }}"
                                        class="transition-all duration-300 hover:opacity-80"
                                        title="Others: {{ round($percent, 1) }}%"/>
                            @endif
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="space-y-2">
                <h3 class="text-lg font-medium text-text mb-4">Legend</h3>
                @foreach($topForPie as $nationality => $data)
                    @php
                        $color = $pieColors[$loop->iteration - 1] ?? '#94a3b8';
                    @endphp
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface-2 transition-colors">
                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $color }}"></div>
                        <div class="flex-1">
                            <span class="text-sm font-medium text-text">{{ $nationality }}</span>
                            <span class="text-sm text-muted ml-2">({{ $data['count']}})</span>
                        </div>
                        <span class="text-sm font-bold text-text">{{ $data['percentage'] }}%</span>
                    </div>
                @endforeach
                @if($othersCount > 0)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface-2 transition-colors">
                        <div class="w-4 h-4 rounded-full bg-muted"></div>
                        <div class="flex-1">
                            <span class="text-sm font-medium text-text">Others</span>
                            <span class="text-sm text-muted ml-2">({{ $othersCount }})</span>
                        </div>
                        <span class="text-sm font-bold text-text">{{ round(($othersCount / $totalForChart) * 100, 1) }}%</span>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-muted">No nationality data available for the selected filters</p>
        </div>
    @endif
</div>
