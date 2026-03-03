<div>
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-accent/20 border border-accent/30 text-accent">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl">
        <form wire:submit="save">
            <div class="card">
                <div class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" value="Name *" />
                        <x-text-input
                            id="name"
                            wire:model="name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            placeholder="Full name"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email *" />
                        <x-text-input
                            id="email"
                            wire:model="email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            placeholder="you@example.com"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" value="Password {{ $isEditing ? '(leave blank to keep current)' : '*'}} " />
                        <x-text-input
                            id="password"
                            wire:model="password"
                            type="password"
                            class="mt-1 block w-full"
                            {{ !$isEditing ? 'required' : '' }}
                            placeholder="Min. 8 characters"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        @if(!$isEditing)
                            <p class="mt-1 text-xs text-muted">Must be at least 8 characters</p>
                        @endif
                    </div>

                    <!-- Password Confirmation -->
                    @if(!$isEditing || $password)
                        <div>
                            <x-input-label for="password_confirmation" value="Confirm Password {{ $isEditing ? '(if changing)' : '*'}}" />
                            <x-text-input
                                id="password_confirmation"
                                wire:model="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                {{ !$isEditing ? 'required' : '' }}
                                placeholder="Confirm password"
                            />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    @endif

                    <!-- Roles -->
                    <div>
                        <x-input-label value="Roles *" />
                        <div class="mt-2 space-y-2">
                            @foreach($allRoles as $role)
                                <label class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        wire:model.live="roles"
                                        value="{{ $role->id }}"
                                        class="rounded border-border bg-surface-2 text-primary focus:ring-primary"
                                    />
                                    <span class="text-sm text-text">
                                        {{ ucfirst($role->name) }}
                                        <span class="text-muted">@if($role->name === 'superadmin') Full system access
@elseif($role->name === 'admin') Manage users, departments, issue types
@else) Create and manage issues
@endif</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        <p class="mt-1 text-xs text-muted">Select at least one role</p>
                    </div>

                    <!-- Active Status -->
                    <div>
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                wire:model="is_active"
                                class="rounded border-border bg-surface-2 text-primary focus:ring-primary"
                            />
                            <span class="text-sm font-medium text-text">Active User</span>
                        </label>
                        <p class="mt-1 text-xs text-muted">Inactive users cannot log in to the system</p>
                    </div>

                    <!-- Warning for editing self -->
                    @if($isEditing && $user->id === auth()->id())
                        <div class="p-4 rounded-lg bg-warning/20 border border-warning/30">
                            <p class="text-sm text-warning">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                You are editing your own profile. Be careful when changing roles or deactivating.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-surface-2 border-t border-border flex items-center justify-end gap-3">
                    <button type="button" wire:click="cancel" class="btn btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ $isEditing ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
