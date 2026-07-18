<!-- Trends Over Time -->
<div class="space-y-6">
    <!-- Group By Selector -->
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-text">Nationality Trends</h3>
        <div class="flex items-center gap-2">
            <label class="text-sm text-muted">Group by:</label>
            <select wire:model.live="trendGroupBy"
                    class="bg-surface-2 border border-border text-text rounded-lg px-3 py-1.5 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
            </select>
        </div>
    </div>

    @if($report['nationality_trend']->count() > 0)
        @php
            // Group trend data by period
            $trendByPeriod = $report['nationality_trend']->groupBy('period');
            $periods = $trendByPeriod->keys()->sort()->values();

            // Get top 5 nationalities overall + "Others"
            $topNationalities = $report['by_nationality']->keys()->take(5);
            $trendColors = [
                '#a855f7', // purple-500
                '#3b82f6', // primary
                '#06b6d4', // accent
                '#22c55e', // success
                '#f59e0b', // warning
                '#94a3b8', // muted (others)
            ];
        @endphp

        <!-- Line Chart -->
        <div class="glass-card rounded-xl p-6">
            @if($periods->count() > 1)
                @php
                    $maxCount = $trendByPeriod->max(function($periodData) {
                        return $periodData->sum('count');
                    });
                    $chartWidth = 800;
                    $chartHeight = 300;
                    $paddingLeft = 50;
                    $paddingRight = 30;
                    $paddingTop = 20;
                    $paddingBottom = 40;
                    $usableWidth = $chartWidth - $paddingLeft - $paddingRight;
                    $usableHeight = $chartHeight - $paddingTop - $paddingBottom;

                    $stepX = $periods->count() > 1 ? $usableWidth / ($periods->count() - 1) : $usableWidth;

                    // Determine which labels to show
                    $labelInterval = max(1, floor($periods->count() / 8));
                @endphp

                <div class="relative">
                    <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-80 overflow-visible" preserveAspectRatio="xMidYMid meet">
                        <!-- Grid lines -->
                        @for($i = 0; $i <= 4; $i++)
                            @php
                                $y = $paddingTop + ($usableHeight * $i / 4);
                                $labelValue = round($maxCount * (4 - $i) / 4);
                            @endphp
                            <line x1="{{ $paddingLeft }}" y1="{{ $y }}" x2="{{ $chartWidth - $paddingRight }}" y2="{{ $y }}"
                                  stroke="rgb(var(--border) / 0.3)" stroke-width="1" stroke-dasharray="4 4"/>
                            <text x="{{ $paddingLeft - 10 }}" y="{{ $y + 4 }}"
                                  text-anchor="end" class="text-xs" fill="rgb(var(--muted))">{{ $labelValue }}</text>
                        @endfor

                        <!-- X-axis labels -->
                        @foreach($periods as $index => $period)
                            @if($index % $labelInterval === 0 || $index === $periods->count() - 1)
                                @php
                                    $x = $paddingLeft + ($periods->count() > 1 ? $index * $stepX : $usableWidth / 2);
                                    $label = $trendGroupBy === 'month' ? substr($period, 5) : $period;
                                @endphp
                                <text x="{{ $x }}" y="{{ $chartHeight - 10 }}"
                                      text-anchor="middle"
                                      class="text-xs"
                                      fill="rgb(var(--muted))">{{ $label }}</text>
                            @endif
                        @endforeach

                        <!-- Trend lines for top nationalities -->
                        @foreach($topNationalities as $nationality)
                            @php
                                $color = $trendColors[$loop->iteration - 1] ?? '#94a3b8';
                                $pathD = '';
                                $points = [];

                                foreach($periods as $index => $period) {
                                    $count = $trendByPeriod->get($period)?->where('nationality', $nationality)->first()?->count ?? 0;
                                    $x = $paddingLeft + ($periods->count() > 1 ? $index * $stepX : $usableWidth / 2);
                                    $y = $paddingTop + $usableHeight - (($count / max($maxCount, 1)) * $usableHeight);
                                    $points[] = ['x' => $x, 'y' => $y, 'count' => $count];

                                    if ($index === 0) {
                                        $pathD .= 'M ' . $x . ' ' . $y;
                                    } else {
                                        $pathD .= ' L ' . $x . ' ' . $y;
                                    }
                                }
                            @endphp

                            <!-- Line -->
                            <path d="{{ $pathD }}"
                                  fill="none"
                                  stroke="{{ $color }}"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  class="transition-all duration-300"/>

                            <!-- Data points -->
                            @foreach($points as $point)
                                @if($point['count'] > 0)
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3"
                                            fill="{{ $color }}"
                                            class="transition-all duration-200 hover:r-5"/>
                                @endif
                            @endforeach
                        @endforeach
                    </svg>
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap gap-4 mt-4">
                    @foreach($topNationalities as $nationality)
                        @php
                            $color = $trendColors[$loop->iteration - 1] ?? '#94a3b8';
                        @endphp
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $color }}"></div>
                            <span class="text-sm text-text">{{ $nationality }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-muted py-8">Need at least 2 periods to show trend</p>
            @endif
        </div>

        <!-- Trend Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($topNationalities->take(3) as $nationality)
                @php
                    $firstPeriod = $periods->first();
                    $lastPeriod = $periods->last();

                    $firstCount = $trendByPeriod->get($firstPeriod)?->where('nationality', $nationality)->first()?->count ?? 0;
                    $lastCount = $trendByPeriod->get($lastPeriod)?->where('nationality', $nationality)->first()?->count ?? 0;

                    $change = $firstCount > 0 ? round((($lastCount - $firstCount) / $firstCount) * 100, 1) : 0;
                    $isPositive = $change > 0;
                    $isNegative = $change < 0;
                @endphp
                <div class="glass-card rounded-xl p-4">
                    <p class="text-sm text-muted mb-1">{{ $nationality }}</p>
                    <div class="flex items-end gap-2">
                        <p class="text-2xl font-bold text-text">{{ $lastCount }}</p>
                        @if($change !== 0)
                            <p class="text-sm font-medium {{ $isPositive ? 'text-success' : ($isNegative ? 'text-danger' : 'text-muted') }}">
                                {{ $isPositive ? '+' : '' }}{{ $change }}%
                            </p>
                        @endif
                    </div>
                    <p class="text-xs text-muted mt-1">{{ $firstPeriod }} → {{ $lastPeriod }}</p>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            <p class="text-muted">No trend data available for the selected period</p>
        </div>
    @endif
</div>
