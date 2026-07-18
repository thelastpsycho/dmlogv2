<!-- Tree View: Department -> Issue Type -> Nationality -->
<div class="space-y-4" x-data="{
    expandedDepts: {},
    expandedTypes: {},
    toggleDept(deptName) {
        if (this.expandedDepts[deptName]) {
            delete this.expandedDepts[deptName];
        } else {
            this.expandedDepts[deptName] = true;
        }
    },
    toggleType(key) {
        if (this.expandedTypes[key]) {
            delete this.expandedTypes[key];
        } else {
            this.expandedTypes[key] = true;
        }
    },
    isDeptExpanded(deptName) {
        return !!this.expandedDepts[deptName];
    },
    isTypeExpanded(key) {
        return !!this.expandedTypes[key];
    },
    expandAll() {
        @php
            $deptNames = $report['tree_hierarchy']->pluck('name');
        @endphp
        @foreach($deptNames as $dept)
            this.expandedDepts[{{ \Illuminate\Support\Js::encode($dept) }}] = true;
        @endforeach
    },
    collapseAll() {
        this.expandedDepts = {};
        this.expandedTypes = {};
    }
}">
    @if($report['tree_hierarchy']->count() > 0)
        <!-- Header with controls -->
        <div class="glass-card rounded-xl p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-text">Hierarchy Tree</h3>
                    <p class="text-sm text-muted">Department → Issue Type → Nationality</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="expandAll()" class="px-3 py-1.5 text-sm font-medium text-text bg-surface-2 hover:bg-surface-3 rounded-lg transition-colors">
                        Expand All
                    </button>
                    <button @click="collapseAll()" class="px-3 py-1.5 text-sm font-medium text-text bg-surface-2 hover:bg-surface-3 rounded-lg transition-colors">
                        Collapse All
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="glass-card rounded-xl p-4">
                <p class="text-sm text-muted mb-1">Departments</p>
                <p class="text-2xl font-bold text-text">{{ $report['tree_hierarchy']->count() }}</p>
            </div>
            <div class="glass-card rounded-xl p-4">
                <p class="text-sm text-muted mb-1">Total Issue Types</p>
                <p class="text-2xl font-bold text-text">{{ $report['tree_hierarchy']->sum(function($dept) { return count($dept['issue_types']); }) }}</p>
            </div>
            <div class="glass-card rounded-xl p-4">
                <p class="text-sm text-muted mb-1">Total Nationality Nodes</p>
                <p class="text-2xl font-bold text-text">{{ $report['tree_hierarchy']->sum(function($dept) { return collect($dept['issue_types'])->sum(function($type) { return count($type['nationalities']); }); }) }}</p>
            </div>
        </div>

        <!-- Tree Structure -->
        <div class="space-y-3">
            @foreach($report['tree_hierarchy'] as $deptIndex => $department)
                <div class="glass-card rounded-xl overflow-hidden">
                    <!-- Department Level -->
                    <div class="flex items-center justify-between p-4 bg-surface-2/50 hover:bg-surface-2 cursor-pointer transition-colors"
                         @click="toggleDept({{ \Illuminate\Support\Js::encode($department['name']) }})">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-500/5 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-500 transition-transform duration-200"
                                     :class="isDeptExpanded({{ \Illuminate\Support\Js::encode($department['name']) }}) ? 'rotate-90' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-text">{{ $department['name'] }}</h4>
                                <p class="text-xs text-muted">{{ count($department['issue_types']) }} issue types</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold text-purple-500">{{ $department['total'] }}</span>
                            <span class="text-xs text-muted">issues</span>
                        </div>
                    </div>

                    <!-- Issue Types Level (Collapsible) -->
                    <div class="border-t border-border/50"
                         x-show="isDeptExpanded({{ \Illuminate\Support\Js::encode($department['name']) }})"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="p-3 space-y-2">
                            @foreach($department['issue_types'] as $typeIndex => $issueType)
                                @php
                                    $typeKey = $department['name'] . '|' . $issueType['name'];
                                @endphp

                                <!-- Issue Type Level -->
                                <div class="rounded-lg bg-surface-2/30 overflow-hidden">
                                    <div class="flex items-center justify-between p-3 hover:bg-surface-2/50 cursor-pointer transition-colors"
                                         @click="toggleType({{ \Illuminate\Support\Js::encode($typeKey) }})">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-primary transition-transform duration-200"
                                                 :class="isTypeExpanded({{ \Illuminate\Support\Js::encode($typeKey) }}) ? 'rotate-90' : ''"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                            <span class="text-sm font-medium text-text">{{ $issueType['name'] }}</span>
                                            <span class="text-xs text-muted">({{ count($issueType['nationalities']) }} nationalities)</span>
                                        </div>
                                        <span class="text-sm font-bold text-primary">{{ $issueType['total'] }}</span>
                                    </div>

                                    <!-- Nationalities Level (Collapsible) -->
                                    <div class="border-t border-border/30"
                                         x-show="isTypeExpanded({{ \Illuminate\Support\Js::encode($typeKey) }})"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0">
                                        <div class="p-2 space-y-1">
                                            @foreach($issueType['nationalities'] as $nationality)
                                                @php
                                                    $maxTypeCount = collect($department['issue_types'])->max('total');
                                                    $percentage = $maxTypeCount > 0 ? ($nationality['count'] / $issueType['total']) * 100 : 0;
                                                @endphp
                                                <div class="flex items-center justify-between p-2 rounded bg-surface-1/50 hover:bg-surface-2/30 transition-colors">
                                                    <div class="flex items-center gap-2 flex-1">
                                                        <span class="text-sm text-text">{{ $nationality['name'] }}</span>
                                                        <div class="flex-1 h-1.5 bg-surface-2 rounded-full overflow-hidden max-w-[100px]">
                                                            <div class="h-full bg-gradient-to-r from-accent to-accent/80 rounded-full"
                                                                 style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-sm font-bold text-text">{{ $nationality['count'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Legend -->
        <div class="glass-card rounded-xl p-4 mt-4">
            <div class="flex flex-wrap items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-gradient-to-br from-purple-500/20 to-purple-500/5"></div>
                    <span class="text-muted">Department</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-surface-2/30"></div>
                    <span class="text-muted">Issue Type</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-surface-1/50"></div>
                    <span class="text-muted">Nationality</span>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
            </svg>
            <p class="text-muted">No tree data available</p>
        </div>
    @endif
</div>
