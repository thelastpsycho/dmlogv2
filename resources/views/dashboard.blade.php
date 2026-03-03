<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <!-- Welcome Card -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-text">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="text-muted mt-1">Here's what's happening with your issues today.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Open Issues -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted">Open Issues</p>
                    <p class="text-2xl font-bold text-text mt-1">{{ \App\Models\Issue::query()->where('status', 'open')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-accent/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Closed Today -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted">Closed Today</p>
                    <p class="text-2xl font-bold text-text mt-1">{{ \App\Models\Issue::query()->where('status', 'closed')->whereDate('closed_at', today())->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-primary/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Urgent Issues -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted">Urgent Issues</p>
                    <p class="text-2xl font-bold text-text mt-1">{{ \App\Models\Issue::query()->where('status', 'open')->where('priority', 'urgent')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-danger/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- My Issues -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted">Assigned to Me</p>
                    <p class="text-2xl font-bold text-text mt-1">{{ \App\Models\Issue::query()->where('assigned_to', auth()->id())->where('status', 'open')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-warning/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Issues -->
    <div class="card">
        <div class="p-6 border-b border-border flex items-center justify-between">
            <h2 class="text-lg font-semibold text-text">Recent Issues</h2>
            @can('create', \App\Models\Issue::class)
                <a href="{{ route('issues.create') }}" class="btn btn-primary text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Issue
                </a>
            @endcan
        </div>
        @php
            $recentIssues = \App\Models\Issue::query()
                ->with(['departments', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        @endphp
        @if($recentIssues->count() > 0)
            <div class="divide-y divide-border">
                @foreach($recentIssues as $issue)
                    <div class="p-4 flex items-center justify-between hover:bg-surface-2 transition-smooth">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <span class="badge {{ match($issue->priority) {
                                    'urgent' => 'badge-danger',
                                    'high' => 'badge-warning',
                                    default => 'badge-muted'
                                } }}">
                                    {{ ucfirst($issue->priority) }}
                                </span>
                                <span class="badge {{ $issue->status === 'open' ? 'badge-success' : 'badge-muted' }}">
                                    {{ ucfirst($issue->status) }}
                                </span>
                                <h3 class="font-medium text-text">{{ $issue->title }}</h3>
                            </div>
                            <p class="text-sm text-muted mt-1">
                                {{ $issue->departments->pluck('name')->join(', ') ?: 'No department' }}
                                • {{ $issue->createdBy->name ?? 'Unknown' }}
                                • {{ $issue->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ route('issues.show', $issue) }}" class="text-primary hover:underline text-sm">View</a>
                    </div>
                @endforeach
            </div>
            <div class="p-4 border-t border-border">
                <a href="{{ route('issues.index') }}" class="text-primary hover:underline text-sm font-medium">View all issues →</a>
            </div>
        @else
            <div class="p-12 text-center text-muted">
                <p class="mb-4">No issues yet. Create your first issue to get started.</p>
                @can('create', \App\Models\Issue::class)
                    <a href="{{ route('issues.create') }}" class="btn btn-primary">Create Issue</a>
                @endcan
            </div>
        @endif
    </div>
</x-app-layout>
