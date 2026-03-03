<div>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-text">Logbook</h1>
                <p class="text-muted">Printable list of issues with filtering options</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-6" data-no-print>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <x-input-label for="date_from" value="From Date" />
                        <input
                            type="date"
                            id="date_from"
                            wire:model.live="dateFrom"
                            class="mt-1 w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary"
                        />
                    </div>
                    <div>
                        <x-input-label for="date_to" value="To Date" />
                        <input
                            type="date"
                            id="date_to"
                            wire:model.live="dateTo"
                            class="mt-1 w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary"
                        />
                    </div>
                    <div>
                        <x-input-label for="department" value="Department" />
                        <select id="department" wire:model.live="departmentId" class="mt-1 w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="issue_type" value="Issue Type" />
                        <select id="issue_type" wire:model.live="issueTypeId" class="mt-1 w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                            <option value="">All Types</option>
                            @foreach($issueTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" wire:model.live="status" class="mt-1 w-full bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                            <option value="">All Statuses</option>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
                @if($dateFrom || $dateTo || $departmentId || $issueTypeId || $status)
                    <div class="mt-4">
                        <button wire:click="clearFilters" class="text-primary hover:underline text-sm">Clear all filters</button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Results Summary -->
        <div class="mb-4 flex items-center justify-between">
            <p class="text-sm text-muted">
                {{ $issues->count() }} issue{{ $issues->count() !== 1 ? 's' : '' }} found
            </p>
            @if($dateFrom || $dateTo)
                <p class="text-sm text-muted">
                    @if($dateFrom && $dateTo)
                        {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                    @elseif($dateFrom)
                        From {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
                    @else
                        Until {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                    @endif
                </p>
            @endif
        </div>

        <!-- Issues Table -->
        <div class="card">
            @if($issues->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-border">
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Departments</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Created</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Closed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($issues as $issue)
                                <tr class="hover:bg-surface-2 transition-smooth">
                                    <td class="px-4 py-3 text-sm font-mono text-muted">#{{ $issue->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs">
                                            <p class="text-sm font-medium text-text truncate">{{ $issue->title }}</p>
                                            @if($issue->description)
                                                <p class="text-xs text-muted truncate">{{ \Illuminate\Support\Str::limit(strip_tags($issue->description), 80) }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($issue->departments as $dept)
                                                <span class="badge badge-muted text-xs">{{ $dept->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @foreach($issue->issueTypes as $type)
                                            <span class="badge badge-muted text-xs">{{ $type->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($issue->priority)
                                            <span class="badge badge-{{ $issue->priority === 'urgent' ? 'danger' : ($issue->priority === 'high' ? 'warning' : 'muted') }} text-xs">
                                                {{ ucfirst($issue->priority) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($issue->closed_at)
                                            <span class="badge badge-accent text-xs">Closed</span>
                                        @else
                                            <span class="badge badge-warning text-xs">Open</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-muted">{{ $issue->created_at?->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-muted">{{ $issue->closed_at?->format('M d, Y') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-text mb-2">No issues found</h3>
                    <p class="text-muted">Try adjusting your filters or date range.</p>
                </div>
            @endif
        </div>

        <!-- Print Styles -->
        <style>
            @media print {
                [data-no-print] {
                    display: none !important;
                }
                body {
                    background: white;
                    color: black;
                }
                .card {
                    border: 1px solid #ddd;
                    box-shadow: none;
                }
                table {
                    font-size: 10pt;
                }
                a {
                    color: black;
                    text-decoration: none;
                }
            }
        </style>
    </div>
</div>
