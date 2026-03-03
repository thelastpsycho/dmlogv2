<div>
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-accent/20 border border-accent/30 text-accent">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-lg bg-danger/20 border border-danger/30 text-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text">Departments</h1>
            <p class="text-muted">Manage hotel departments for issue tracking</p>
        </div>
        @can('create', App\Models\Department::class)
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Department
            </a>
        @endcan
    </div>

    <!-- Search -->
    <div class="card mb-6">
        <div class="p-4">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search departments..."
                class="w-full bg-surface-2 border border-border text-text placeholder-muted rounded-lg px-4 py-2 focus:border-primary focus:ring-primary"
            />
        </div>
    </div>

    <!-- Departments Table -->
    <div class="card">
        @if($departments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider cursor-pointer hover:text-text"
                                wire:click="sortBy('name')">
                                <div class="flex items-center gap-1">
                                    Name
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->sortIcon('name') }}"/>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider cursor-pointer hover:text-text"
                                wire:click="sortBy('issues_count')">
                                <div class="flex items-center gap-1">
                                    Issues
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->sortIcon('issues_count') }}"/>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($departments as $department)
                            <tr class="hover:bg-surface-2 transition-smooth">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-text">{{ $department->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-muted">{{ $department->description ?: '—' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge badge-muted">{{ $department->issues_count }} issues</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $department)
                                            <a href="{{ route('admin.departments.edit', $department) }}"
                                               class="text-primary hover:underline text-sm">Edit</a>
                                        @endcan
                                        @if($department->issues_count === 0)
                                            @can('delete', $department)
                                                <button wire:click="deleteDepartment({{ $department->id }})"
                                                        wire:confirm="Are you sure you want to delete this department?"
                                                        class="text-danger hover:underline text-sm">Delete</button>
                                                    @endcan
                                        @else
                                            <span class="text-muted text-xs">Cannot delete</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-border">
                {{ $departments->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="text-lg font-medium text-text mb-2">No departments found</h3>
                <p class="text-muted mb-6">Get started by creating a new department.</p>
                @can('create', App\Models\Department::class)
                    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">Add Department</a>
                @endcan
            </div>
        @endif
    </div>
</div>
