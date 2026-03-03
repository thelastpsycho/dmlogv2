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
            <h1 class="text-2xl font-bold text-text">Users</h1>
            <p class="text-muted">Manage system users and their roles</p>
        </div>
        @can('create', App\Models\User::class)
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add User
            </a>
        @endcan
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
                        placeholder="Search users by name or email..."
                        class="w-full bg-surface-2 border border-border text-text placeholder-muted rounded-lg px-4 py-2 focus:border-primary focus:ring-primary"
                    />
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-3">
                    <select wire:model.live="roleFilter" class="bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="statusFilter" class="bg-surface-2 border border-border text-text rounded-lg px-3 py-2 focus:border-primary focus:ring-primary">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    @if($search || $roleFilter || $statusFilter)
                        <button wire:click="clearFilters" class="text-primary hover:underline text-sm">Clear filters</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider cursor-pointer hover:text-text"
                                wire:click="sortBy('name')">
                                <div class="flex items-center gap-1">
                                    User
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->sortIcon('name') }}"/>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                Roles
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider cursor-pointer hover:text-text"
                                wire:click="sortBy('email')">
                                <div class="flex items-center gap-1">
                                    Email
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->sortIcon('email') }}"/>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider cursor-pointer hover:text-text"
                                wire:click="sortBy('is_active')">
                                <div class="flex items-center gap-1">
                                    Status
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $this->sortIcon('is_active') }}"/>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($users as $user)
                            <tr class="hover:bg-surface-2 transition-smooth">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-semibold">
                                            {{ $user->name[0] }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-text">{{ $user->name }}</div>
                                            @if($user->id === auth()->id())
                                                <span class="text-xs text-muted">(You)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-muted">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-muted">{{ $user->email }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $user)
                                            @if($user->id !== auth()->id())
                                                <button wire:click="toggleUserStatus({{ $user->id }})"
                                                        class="text-sm {{ $user->is_active ? 'text-warning' : 'text-accent' }} hover:underline">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="text-primary hover:underline text-sm">Edit</a>
                                        @endcan
                                        @can('delete', $user)
                                            @if($user->id !== auth()->id())
                                                <button wire:click="deleteUser({{ $user->id }})"
                                                        wire:confirm="Are you sure you want to delete this user?"
                                                        class="text-danger hover:underline text-sm">Delete</button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-border">
                {{ $users->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-text mb-2">No users found</h3>
                <p class="text-muted mb-6">Get started by creating a new user.</p>
                @can('create', App\Models\User::class)
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
                @endcan
            </div>
        @endif
    </div>
</div>
