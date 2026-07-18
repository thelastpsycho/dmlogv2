<!-- Detailed Table -->
<div class="space-y-4">
    @if($report['detailed_table']->count() > 0)
        <div class="glass-card rounded-xl overflow-hidden">
            <div class="p-4 border-b border-border/50 flex items-center justify-between">
                <h3 class="text-base font-medium text-text">Detailed Nationality Statistics</h3>
                <span class="text-sm text-muted">{{ $report['detailed_table']->count() }} nationalities</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-2">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-muted">Nationality</th>
                            <th class="px-4 py-3 text-right font-medium text-muted">Total</th>
                            <th class="px-4 py-3 text-right font-medium text-muted">Open</th>
                            <th class="px-4 py-3 text-right font-medium text-muted">Closed</th>
                            <th class="px-4 py-3 text-right font-medium text-muted">Close Rate</th>
                            <th class="px-4 py-3 text-right font-medium text-muted">Avg Close Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @foreach($report['detailed_table'] as $item)
                            @php
                                $closeRate = $item->total > 0 ? round(($item->closed / $item->total) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-surface-2/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-text">{{ $item->nationality }}</td>
                                <td class="px-4 py-3 text-right font-bold text-text">{{ $item->total }}</td>
                                <td class="px-4 py-3 text-right text-warning">{{ $item->open }}</td>
                                <td class="px-4 py-3 text-right text-success">{{ $item->closed }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="w-16 h-2 bg-surface-2 rounded-full overflow-hidden">
                                            <div class="h-full bg-success rounded-full"
                                                 style="width: {{ $closeRate }}%"></div>
                                        </div>
                                        <span class="text-xs text-muted min-w-[40px]">{{ $closeRate }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right text-text">
                                    {{ $item->avg_close_time ? $item->avg_close_time . 'h' : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass-card rounded-xl p-4">
                <p class="text-sm text-muted mb-1">Total Nationalities</p>
                <p class="text-2xl font-bold text-text">{{ $report['detailed_table']->count() }}</p>
            </div>

            <div class="glass-card rounded-xl p-4">
                <p class="text-sm text-muted mb-1">Most Active</p>
                <p class="text-lg font-bold text-text truncate">{{ $report['detailed_table']->first()->nationality }}</p>
                <p class="text-xs text-muted">{{ $report['detailed_table']->first()->total }} issues</p>
            </div>

            @php
                $bestCloseRate = $report['detailed_table']->filter(function($item) {
                    return $item->total >= 5; // Only consider nationalities with 5+ issues
                })->sortByDesc(function($item) {
                    return $item->total > 0 ? ($item->closed / $item->total) : 0;
                })->first();

                $fastestCloseTime = $report['avg_close_time_by_nationality']->filter(function($time, $nationality) use ($report) {
                    // Only consider nationalities with 5+ issues
                    $total = $report['detailed_table']->firstWhere('nationality', $nationality)->total ?? 0;
                    return $total >= 5;
                })->sort()->first();
            @endphp

            @if($bestCloseRate)
                @php
                    $bestRate = $bestCloseRate->total > 0 ? round(($bestCloseRate->closed / $bestCloseRate->total) * 100, 1) : 0;
                @endphp
                <div class="glass-card rounded-xl p-4">
                    <p class="text-sm text-muted mb-1">Best Close Rate</p>
                    <p class="text-lg font-bold text-text truncate">{{ $bestCloseRate->nationality }}</p>
                    <p class="text-xs text-success">{{ $bestRate }}% closed</p>
                </div>
            @endif

            @if($fastestCloseTime)
                <div class="glass-card rounded-xl p-4">
                    <p class="text-sm text-muted mb-1">Fastest Resolution</p>
                    @php
                        $nationality = array_key_first($fastestCloseTime);
                    @endphp
                    <p class="text-lg font-bold text-text truncate">{{ $nationality }}</p>
                    <p class="text-xs text-accent">{{ $fastestCloseTime[$nationality] }}h avg</p>
                </div>
            @endif
        </div>

        <!-- Export Note -->
        <div class="glass-card rounded-xl p-4 bg-surface-2/50">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-muted mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-text">
                        <strong class="font-medium">Tip:</strong>
                        Use the filters above to narrow down data by date range, department, or status. Click on any nationality to see related issues.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-muted">No detailed table data available</p>
        </div>
    @endif
</div>
