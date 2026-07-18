<!-- Issue Type by Nationality -->
<div class="space-y-6">
    @if($report['by_issue_type']->count() > 0)
        @php
            // Get colors for nationalities (top 10 + others)
            $issueTypeColors = [
                'from-purple-500 to-purple-600',
                'from-blue-500 to-blue-600',
                'from-cyan-500 to-cyan-600',
                'from-green-500 to-green-600',
                'from-yellow-500 to-yellow-600',
                'from-orange-500 to-orange-600',
                'from-red-500 to-red-600',
                'from-pink-500 to-pink-600',
                'from-indigo-500 to-indigo-600',
                'from-teal-500 to-teal-600',
                'from-gray-500 to-gray-600',
            ];
        @endphp

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-card rounded-xl p-4">
                <p class="text-sm text-muted mb-1">Total Issue Types</p>
                <p class="text-3xl font-bold text-text">{{ $report['by_issue_type']->count() }}</p>
            </div>

            @php
                $topIssueType = $report['by_issue_type']->map(function($group) {
                    return [
                        'name' => $group->first()['issue_type'],
                        'total' => $group->sum('count')
                    ];
                })->sortByDesc('total')->first();
            @endphp

            @if($topIssueType)
                <div class="glass-card rounded-xl p-4">
                    <p class="text-sm text-muted mb-1">Most Common Issue Type</p>
                    <p class="text-lg font-bold text-text truncate">{{ $topIssueType['name'] }}</p>
                    <p class="text-xs text-muted">{{ $topIssueType['total'] }} issues</p>
                </div>
            @endif

            @php
                $mostDiverse = $report['by_issue_type']->map(function($group) {
                    return [
                        'name' => $group->first()['issue_type'],
                        'nationality_count' => $group->count()
                    ];
                })->sortByDesc('nationality_count')->first();
            @endphp

            @if($mostDiverse)
                <div class="glass-card rounded-xl p-4">
                    <p class="text-sm text-muted mb-1">Most Diverse (by Nationality)</p>
                    <p class="text-lg font-bold text-text truncate">{{ $mostDiverse['name'] }}</p>
                    <p class="text-xs text-muted">{{ $mostDiverse['nationality_count'] }} nationalities</p>
                </div>
            @endif
        </div>

        <!-- Issue Type Breakdown Cards -->
        <div class="space-y-4">
            @foreach($report['by_issue_type']->take(10) as $issueTypeName => $nationalityData)
                @php
                    $totalCount = $nationalityData->sum('count');
                    $topNationalities = $nationalityData->sortByDesc('count')->take(8);
                    $otherCount = $nationalityData->slice(8)->sum('count');
                @endphp

                <div class="glass-card rounded-xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-text">{{ $issueTypeName }}</h3>
                        <span class="text-sm text-muted">{{ $totalCount }} issues</span>
                    </div>

                    <!-- Horizontal Bar Chart -->
                    <div class="space-y-2">
                        @foreach($topNationalities as $index => $data)
                            @php
                                $percentage = $totalCount > 0 ? round(($data['count'] / $totalCount) * 100, 1) : 0;
                                $colorIndex = $index % count($issueTypeColors);
                            @endphp
                            <div class="group">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-text">{{ $data['nationality'] }}</span>
                                    <span class="text-sm font-bold text-text">{{ $data['count'] }} <span class="text-muted font-normal">({{ $percentage }}%)</span></span>
                                </div>
                                <div class="h-2.5 bg-surface-2 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r {{ $issueTypeColors[$colorIndex] }} rounded-full transition-all duration-500 group-hover:opacity-80"
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach

                        @if($otherCount > 0)
                            @php
                                $otherPercentage = $totalCount > 0 ? round(($otherCount / $totalCount) * 100, 1) : 0;
                            @endphp
                            <div class="group">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-muted">Other Nationalities</span>
                                    <span class="text-sm font-bold text-text">{{ $otherCount }} <span class="text-muted font-normal">({{ $otherPercentage }}%)</span></span>
                                </div>
                                <div class="h-2.5 bg-surface-2 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-gray-400 to-gray-500 rounded-full transition-all duration-500 group-hover:opacity-80"
                                         style="width: {{ $otherPercentage }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary Table -->
        <div class="glass-card rounded-xl overflow-hidden">
            <div class="p-4 border-b border-border/50">
                <h3 class="text-base font-medium text-text">Issue Type × Nationality Summary</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-2">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-muted">Issue Type</th>
                            <th class="px-4 py-3 text-left font-medium text-muted">Top Nationality</th>
                            <th class="px-4 py-3 text-right font-medium text-muted">Total Issues</th>
                            <th class="px-4 py-3 text-right font-medium text-muted">Unique Nationalities</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @foreach($report['by_issue_type'] as $issueTypeName => $nationalityData)
                            @php
                                $totalCount = $nationalityData->sum('count');
                                $topNationality = $nationalityData->sortByDesc('count')->first();
                                $uniqueCount = $nationalityData->count();
                            @endphp
                            <tr class="hover:bg-surface-2/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-text">{{ $issueTypeName }}</td>
                                <td class="px-4 py-3 text-text">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">{{ $topNationality['nationality'] }}</span>
                                        <span class="text-muted text-xs">({{ $topNationality['count'] }})</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-text">{{ $totalCount }}</td>
                                <td class="px-4 py-3 text-right text-accent">{{ $uniqueCount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stacked Bar: Top Issue Types by Nationality Distribution -->
        @php
            $topIssueTypesForChart = $report['by_issue_type']->take(6);
            $allNationalitiesInChart = collect();

            foreach($topIssueTypesForChart as $nationalityData) {
                foreach($nationalityData->take(5) as $data) {
                    $allNationalitiesInChart->push($data['nationality']);
                }
            }

            $topNationalitiesForChart = $allNationalitiesInChart->unique()->sort()->values();
        @endphp

        @if($topIssueTypesForChart->count() > 0)
            <div class="glass-card rounded-xl p-6">
                <h3 class="text-base font-medium text-text mb-4">Nationality Distribution by Issue Type</h3>
                <div class="space-y-4">
                    @foreach($topIssueTypesForChart as $issueTypeName => $nationalityData)
                        @php
                            $issueTypeTotal = $nationalityData->sum('count');
                            $topNationalitiesForThisType = $nationalityData->sortByDesc('count')->take(5);
                            $othersCount = $nationalityData->slice(5)->sum('count');
                        @endphp

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-text">{{ $issueTypeName }}</span>
                                <span class="text-xs text-muted">{{ $issueTypeTotal }} total</span>
                            </div>
                            <div class="h-4 bg-surface-2 rounded-full overflow-hidden flex">
                                @foreach($topNationalitiesForThisType as $index => $data)
                                    @php
                                        $percentage = $issueTypeTotal > 0 ? ($data['count'] / $issueTypeTotal) * 100 : 0;
                                        $colorIndex = $index % count($issueTypeColors);
                                    @endphp
                                    <div class="h-full bg-gradient-to-r {{ $issueTypeColors[$colorIndex] }} transition-all duration-500 hover:opacity-80"
                                         style="width: {{ $percentage }}%"
                                         title="{{ $data['nationality'] }}: {{ $data['count'] }} ({{ round($percentage, 1) }}%)"></div>
                                @endforeach

                                @if($othersCount > 0)
                                    @php
                                        $othersPercentage = $issueTypeTotal > 0 ? ($othersCount / $issueTypeTotal) * 100 : 0;
                                    @endphp
                                    <div class="h-full bg-gradient-to-r from-gray-400 to-gray-500 transition-all duration-500 hover:opacity-80"
                                         style="width: {{ $othersPercentage }}%"
                                         title="Others: {{ $othersCount }} ({{ round($othersPercentage, 1) }}%)"></div>
                                @endif
                            </div>

                            <!-- Legend for this issue type -->
                            <div class="flex flex-wrap gap-3 mt-2">
                                @foreach($topNationalitiesForThisType->take(3) as $index => $data)
                                    @php
                                        $colorIndex = $index % count($issueTypeColors);
                                    @endphp
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2 h-2 rounded-full bg-gradient-to-r {{ $issueTypeColors[$colorIndex] }}"></div>
                                        <span class="text-xs text-muted">{{ $data['nationality'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-muted">No issue type data available</p>
        </div>
    @endif
</div>
