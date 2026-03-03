<div>
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-accent/20 border border-accent/30 text-accent">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-border">
            <nav class="-mb-px flex space-x-8">
                <button
                    wire:click="setTab('open')"
                    class="{{ $tab === 'open' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-text hover:border-muted' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-smooth">
                    Open Issues
                    <span class="ml-2 rounded-full bg-primary/20 px-2 py-0.5 text-xs">{{ \App\Models\Issue::open()->count() }}</span>
                </button>
                <button
                    wire:click="setTab('closed')"
                    class="{{ $tab === 'closed' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-text hover:border-muted' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-smooth">
                    Closed Issues
                    <span class="ml-2 rounded-full bg-muted/20 px-2 py-0.5 text-xs">{{ \App\Models\Issue::closed()->count() }}</span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-6">
        <div class="p-4">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search issues..."
                        class="w-full bg-surface-2 border border-border text-text placeholder-muted rounded-lg px-4 py-2 focus:border-primary focus:ring-primary"
                    />
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-3">
                    <select wire:model.live="department_id" class="bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                        <option value="">All Departments</option>
                        @foreach($this->departments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="issue_type_id" class="bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                        <option value="">All Types</option>
                        @foreach($this->issueTypes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="priority" class="bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                        <option value="">All Priorities</option>
                        @foreach($this->priorities as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="assigned_to" class="bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                        <option value="">All Users</option>
                        @foreach($this->users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Active Filters & Clear -->
            @if($search || $department_id || $issue_type_id || $priority || $assigned_to)
                <div class="flex items-center gap-2 mt-4">
                    <span class="text-sm text-muted">Active filters:</span>
                    @if($search)
                        <span class="badge badge-muted">Search: {{ $search }}</span>
                    @endif
                    @if($department_id)
                        <span class="badge badge-muted">{{ $this->departments[$department_id] ?? '' }}</span>
                    @endif
                    @if($issue_type_id)
                        <span class="badge badge-muted">{{ $this->issueTypes[$issue_type_id] ?? '' }}</span>
                    @endif
                    @if($priority)
                        <span class="badge badge-muted">{{ ucfirst($priority) }} Priority</span>
                    @endif
                    @if($assigned_to)
                        <span class="badge badge-muted">{{ $this->users[$assigned_to] ?? '' }}</span>
                    @endif
                    <button wire:click="clearFilters" class="text-sm text-primary hover:underline">Clear all</button>
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions -->
    @if(count($selectedIssues) > 0)
        <div class="card mb-6 bg-primary/10 border-primary/30">
            <div class="p-4 flex items-center justify-between">
                <span class="text-sm font-medium text-text">{{ count($selectedIssues) }} issue(s) selected</span>
                <div class="flex gap-2">
                    @if($tab === 'open')
                        <button wire:click="closeSelected" class="btn btn-primary">Close Selected</button>
                    @else
                        <button wire:click="reopenSelected" class="btn btn-primary">Reopen Selected</button>
                    @endif
                    <button wire:click="deleteSelected" class="btn btn-danger">Delete Selected</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Issues List -->
    <div class="card">
        @if($issues->count() > 0)
            <div class="divide-y divide-border">
                @foreach($issues as $issue)
                    <div class="p-4 hover:bg-surface-2 transition-smooth">
                        <div class="flex items-start gap-4">
                            <!-- Checkbox -->
                            <div class="pt-1">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIssues"
                                    value="{{ $issue->id }}"
                                    class="rounded border-border bg-surface-2 text-primary focus:ring-primary"
                                />
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="badge {{ match($issue->priority) {
                                        'urgent' => 'badge-danger',
                                        'high' => 'badge-warning',
                                        default => 'badge-muted'
                                    } }}">
                                        {{ ucfirst($issue->priority) }}
                                    </span>
                                    @foreach($issue->departments as $department)
                                        <span class="badge badge-muted">{{ $department->name }}</span>
                                    @endforeach
                                    @foreach($issue->issueTypes as $type)
                                        <span class="badge badge-muted">{{ $type->name }}</span>
                                    @endforeach
                                </div>

                                <h3 class="text-lg font-medium text-text mb-1">{{ $issue->title }}</h3>

                                @if($issue->description)
                                    <p class="text-sm text-muted mb-2 line-clamp-2">{{ \Illuminate\Support\Str::limit($issue->description, 200) }}</p>
                                @endif

                                <div class="flex items-center gap-4 text-sm text-muted">
                                    <span>
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $issue->createdBy->name ?? 'Unknown' }}
                                    </span>
                                    <span>
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $issue->created_at->diffForHumans() }}
                                    </span>
                                    @if($issue->assignedTo)
                                        <span class="text-primary">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Assigned to {{ $issue->assignedTo->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('issues.show', $issue) }}" class="text-primary hover:underline text-sm">View</a>
                                @can('update', $issue)
                                    <a href="{{ route('issues.edit', $issue) }}" class="text-primary hover:underline text-sm ml-2">Edit</a>
                                @endcan
                                @if($tab === 'open')
                                    @can('close', $issue)
                                        <button wire:click="closeIssue({{ $issue->id }})" class="text-accent hover:underline text-sm ml-2">Close</button>
                                    @endcan
                                @else
                                    @can('reopen', $issue)
                                        <button wire:click="reopenIssue({{ $issue->id }})" class="text-accent hover:underline text-sm ml-2">Reopen</button>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-border">
                {{ $issues->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="text-lg font-medium text-text mb-2">No issues found</h3>
                <p class="text-muted mb-6">Get started by creating a new issue.</p>
                @can('create', App\Models\Issue::class)
                    <a href="{{ route('issues.create') }}" class="btn btn-primary">Create Issue</a>
                @endcan
            </div>
        @endif
    </div>
</div>
