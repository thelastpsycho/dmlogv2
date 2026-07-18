<!-- By Department -->
<div class="space-y-6">
    @if($report['by_nationality_department']->count() > 0)
        @php
            // Group by department
            $byDept = $report['by_nationality_department']->groupBy('department');
            $deptColors = [
                'from-purple-500 to-purple-600',
                'from-primary to-primary/80',
                'from-accent to-accent/80',
                'from-success to-success/80',
                'from-warning to-warning/80',
            ];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($byDept as $department => $data)
                @php
                    $maxCount = $data->max('count') ?? 1;
                    $colorClass = $deptColors[$loop->iteration - 1] ?? 'from-muted to-gray-500';
                @endphp
                <div class="glass-card rounded-xl p-4">
                    <h3 class="text-base font-medium text-text mb-4">{{ $department }}</h3>
                    <div class="space-y-3">
                        @foreach($data->take(10) as $item)
                            <div class="group">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-text group-hover:text-purple-500 transition-colors truncate max-w-[150px]">{{ $item->nationality }}</span>
                                    <span class="text-sm font-bold text-text">{{ $item->count }}</span>
                                </div>
                                <div class="h-2 bg-surface-2 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r {{ $colorClass }} rounded-full transition-all duration-500 group-hover:opacity-80"
                                         style="width: {{ ($item->count / $maxCount) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary Table -->
        <div class="glass-card rounded-xl overflow-hidden">
            <div class="p-4 border-b border-border/50">
                <h3 class="text-base font-medium text-text">Nationality × Department Matrix</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-2">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-muted">Nationality</th>
                            @foreach($byDept->take(5) as $department => $data)
                                <th class="px-4 py-3 text-right font-medium text-muted">{{ $department }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-right font-medium text-muted">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @php
                            // Get all unique nationalities
                            $allNationalities = $report['by_nationality_department']->pluck('nationality')->unique()->take(15);
                        @endphp
                        @foreach($allNationalities as $nationality)
                            @php
                                $total = 0;
                            @endphp
                            <tr class="hover:bg-surface-2/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-text">{{ $nationality }}</td>
                                @foreach($byDept->take(5) as $department => $data)
                                    @php
                                        $count = $data->where('nationality', $nationality)->first()?->count ?? 0;
                                        $total += $count;
                                    @endphp
                                    <td class="px-4 py-3 text-right text-text">{{ $count > 0 ? $count : '-' }}</td>
                                @endforeach
                                <td class="px-4 py-3 text-right font-bold text-text">{{ $total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-muted">No department breakdown data available</p>
        </div>
    @endif
</div>
