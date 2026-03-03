<div>
<!-- Flash Messages -->
@if (session('success'))
    <div class="mb-6 p-4 rounded-lg bg-accent/20 border border-accent/30 text-accent">
        {{ session('success') }}
    </div>
@endif

<!-- Issue Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Issue Card -->
        <div class="card">
            <div class="p-6">
                <!-- Status & Priority Badges -->
                <div class="flex items-center gap-3 mb-4">
                    <span class="badge {{ $this->priorityBadge }}">
                        {{ ucfirst($issue->priority) }} Priority
                    </span>
                    <span class="badge {{ $this->statusBadge }}">
                        {{ ucfirst($issue->status) }}
                    </span>
                    @if($issue->status === 'closed' && $issue->closed_at)
                        <span class="text-sm text-muted">
                            Closed {{ $issue->closed_at->diffForHumans() }}
                        </span>
                    @endif
                </div>

                <!-- Title -->
                <h1 class="text-2xl font-bold text-text mb-4">{{ $issue->title }}</h1>

                <!-- Description -->
                @if($issue->description)
                    <div class="prose prose-invert max-w-none mb-6">
                        <p class="text-muted whitespace-pre-wrap">{{ $issue->description }}</p>
                    </div>
                @endif

                <!-- Metadata -->
                <div class="flex flex-wrap gap-4 text-sm text-muted mb-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Created by {{ $issue->createdBy->name ?? 'Unknown' }}
                        <span class="mx-1">•</span>
                        {{ $issue->created_at->diffForHumans() }}
                    </div>
                    @if($issue->location)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $issue->location }}
                        </div>
                    @endif
                </div>

                <!-- Departments & Types -->
                <div class="flex flex-wrap gap-2">
                    @foreach($issue->departments as $department)
                        <span class="badge badge-muted">{{ $department->name }}</span>
                    @endforeach
                    @foreach($issue->issueTypes as $type)
                        <span class="badge badge-muted">{{ $type->name }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-surface-2 border-t border-border flex items-center justify-between">
                <div class="flex gap-2">
                    @can('update', $issue)
                        <a href="{{ route('issues.edit', $issue) }}" class="btn btn-secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    @endcan
                    @can('issues.export')
                        <button wire:click="exportPDF" class="btn btn-secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export PDF
                        </button>
                    @endcan
                    @if($issue->status === 'open')
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    @endcan
                    @if($issue->status === 'open')
                        @can('close', $issue)
                            <button wire:click="showCloseModal = true" class="btn btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Close Issue
                            </button>
                        @endcan
                    @else
                        @can('reopen', $issue)
                            <button wire:click="showReopenModal = true" class="btn btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reopen Issue
                            </button>
                        @endcan
                    @endif
                </div>
                @can('delete', $issue)
                    <button wire:click="deleteIssue" class="text-danger hover:underline text-sm">
                        Delete Issue
                    </button>
                @endcan
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card">
            <div class="p-6 border-b border-border">
                <h2 class="text-lg font-semibold text-text">Comments</h2>
            </div>
            <div class="divide-y divide-border">
                @if($issue->comments->count() > 0)
                    @foreach($issue->comments as $comment)
                        <div class="p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary text-sm font-semibold flex-shrink-0">
                                    {{ $comment->user->name[0] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-text">{{ $comment->user->name }}</span>
                                        <span class="text-xs text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                        @can('delete', $comment)
                                            <button wire:click="deleteComment({{ $comment->id }})" class="text-danger hover:underline text-xs ml-auto">
                                                Delete
                                            </button>
                                        @endcan
                                    </div>
                                    <p class="text-sm text-muted whitespace-pre-wrap">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-muted">
                        No comments yet. Be the first to comment!
                    </div>
                @endif
            </div>

            <!-- Add Comment Form -->
            <div class="p-4 bg-surface-2 border-t border-border">
                <form wire:submit="addComment">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary text-sm font-semibold flex-shrink-0">
                            {{ auth()->user()->name[0] }}
                        </div>
                        <div class="flex-1">
                            <textarea
                                wire:model="comment"
                                rows="2"
                                class="w-full bg-surface border border-border text-text placeholder-muted rounded-lg px-3 py-2 focus:border-primary focus:ring-primary"
                                placeholder="Add a comment..."
                            ></textarea>
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Assigned To -->
        <div class="card">
            <div class="p-4">
                <h3 class="text-sm font-medium text-muted mb-3">Assigned To</h3>
                @if($issue->assignedTo)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-semibold">
                            {{ $issue->assignedTo->name[0] }}
                        </div>
                        <div>
                            <p class="font-medium text-text">{{ $issue->assignedTo->name }}</p>
                            <p class="text-xs text-muted">{{ $issue->assignedTo->roles->first()->name ?? 'User' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-muted">Unassigned</p>
                @endif
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="p-4 border-b border-border">
                <h3 class="text-sm font-medium text-muted">Activity Log</h3>
            </div>
            <div class="p-4 space-y-4 max-h-96 overflow-y-auto">
                @if($activityLog->count() > 0)
                    @foreach($activityLog as $activity)
                        <div class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xs font-semibold flex-shrink-0">
                                {{ $activity->user->name[0] }}
                            </div>
                            <div>
                                <p class="text-sm text-text">
                                    <span class="font-medium">{{ $activity->user->name }}</span>
                                    <span class="text-muted">{{ $activity->description }}</span>
                                </p>
                                <p class="text-xs text-muted">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-muted">No activity yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Close Modal -->
@if($showCloseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black opacity-75" wire:click="showCloseModal = false"></div>
        <div class="relative bg-surface border border-border rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-text mb-2">Close Issue</h3>
                <p class="text-sm text-muted mb-4">Add a note about why this issue is being closed (optional).</p>
                <textarea
                    wire:model="closeNote"
                    rows="3"
                    class="w-full bg-surface-2 border border-border text-text placeholder-muted rounded-lg px-3 py-2 focus:border-primary focus:ring-primary"
                    placeholder="Resolution note..."
                ></textarea>
            </div>
            <div class="px-6 py-4 bg-surface-2 border-t border-border flex justify-end gap-3">
                <button wire:click="showCloseModal = false" class="btn btn-secondary">Cancel</button>
                <button wire:click="closeIssue" class="btn btn-primary">Close Issue</button>
            </div>
        </div>
    </div>
@endif

<!-- Reopen Modal -->
@if($showReopenModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black opacity-75" wire:click="showReopenModal = false"></div>
        <div class="relative bg-surface border border-border rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-text mb-2">Reopen Issue</h3>
                <p class="text-sm text-muted">Are you sure you want to reopen this issue?</p>
            </div>
            <div class="px-6 py-4 bg-surface-2 border-t border-border flex justify-end gap-3">
                <button wire:click="showReopenModal = false" class="btn btn-secondary">Cancel</button>
                <button wire:click="reopenIssue" class="btn btn-primary">Reopen Issue</button>
            </div>
        </div>
    </div>
@endif
</div>
</div>
