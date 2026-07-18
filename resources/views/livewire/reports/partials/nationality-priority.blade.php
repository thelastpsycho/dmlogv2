<!-- Priority Analysis -->
<div class="space-y-6">
    @if($report['by_nationality_priority']->count() > 0)
        @php
            // Group by nationality
            $byNationality = $report['by_nationality_priority']->groupBy('nationality');
            $priorities = ['urgent', 'high', 'medium', 'low'];
            $priorityColors = [
                'urgent' => 'from-danger to-red-600',
                'high' => 'from-warning to-orange-600',
                'medium' => 'from-accent to-blue-600',
                'low' => 'from-success to-emerald-600',
            ];
            $priorityTextColors = [
                'urgent' => 'text-danger',
                'high' => 'text-warning',
                'medium' => 'text-accent',
                'low' => 'text-success',
            ];
        @endphp

        <!-- Top 10 Nationalities by Priority -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($byNationality->take(10) as $nationality => $data)
                @php
                    $totalCount = $data->sum('count');
                    $maxCount = $data->max('count') ?? 1;
                @endphp
                <div class="glass-card rounded-xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-medium text-text">{{ $nationality }}</h3>
                        <span class="text-sm text-muted">{{ $totalCount }} issues</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($priorities as $priority)
                            @php
                                $count = $data->where('priority', $priority)->first()?->count ?? 0;
                            @endphp
                            @if($count > 0)
                            <div class="group">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium {{ $priorityTextColors[$priority] }} capitalize">{{ $priority }}</span>
                                    <span class="text-xs font-bold text-text">{{ $count }}</span>
                                </div>
                                <div class="h-2 bg-surface-2 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r {{ $priorityColors[$priority] }} rounded-full transition-all duration-500 group-hover:opacity-80"
                                         style="width: {{ ($count / $maxCount) * 100 }}%"></div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Priority Summary Table -->
        <div class="glass-card rounded-xl overflow-hidden">
            <div class="p-4 border-b border-border/50">
                <h3 class="text-base font-medium text-text">Priority Breakdown Summary</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-2">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-muted">Nationality</th>
                            @foreach($priorities as $priority)
                                <th class="px-4 py-3 text-right font-medium {{ $priorityTextColors[$priority] }} capitalize">{{ $priority }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-right font-medium text-muted">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @foreach($byNationality->take(15) as $nationality => $data)
                            @php
                                $total = $data->sum('count');
                            @endphp
                            <tr class="hover:bg-surface-2/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-text">{{ $nationality }}</td>
                                @foreach($priorities as $priority)
                                    @php
                                        $count = $data->where('priority', $priority)->first()?->count ?? 0;
                                    @endphp
                                    <td class="px-4 py-3 text-right {{ $priorityTextColors[$priority] }}">{{ $count > 0 ? $count : '-' }}</td>
                                @endforeach
                                <td class="px-4 py-3 text-right font-bold text-text">{{ $total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Priority Distribution by Status -->
        @if($report['by_nationality_status']->count() > 0)
            @php
                $byStatus = $report['by_nationality_status']->groupBy('nationality');
            @endphp

            <div class="glass-card rounded-xl p-6">
                <h3 class="text-base font-medium text-text mb-4">Resolution Status by Nationality</h3>
                <div class="space-y-4">
                    @foreach($byStatus->take(10) as $nationality => $statusData)
                        @php
                            $total = $statusData->sum('count');
                            $openCount = $statusData->where('status', 'open')->first()?->count ?? 0;
                            $closedCount = $statusData->where('status', 'closed')->first()?->count ?? 0;
                            $openPercent = $total > 0 ? ($openCount / $total) * 100 : 0;
                            $closedPercent = $total > 0 ? ($closedCount / $total) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-text">{{ $nationality }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-warning">{{ $openCount }} open</span>
                                    <span class="text-xs text-success">{{ $closedCount }} closed</span>
                                </div>
                            </div>
                            <div class="h-3 bg-surface-2 rounded-full overflow-hidden flex">
                                <div class="h-full bg-warning transition-all duration-500"
                                     style="width: {{ $openPercent }}%"
                                     title="{{ round($openPercent, 1) }}% open"></div>
                                <div class="h-full bg-success transition-all duration-500"
                                     style="width: {{ $closedPercent }}%"
                                     title="{{ round($closedPercent, 1) }}% closed"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-muted">No priority data available</p>
        </div>
    @endif
</div>
