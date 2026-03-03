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
                            placeholder="e.g., Maintenance, Housekeeping"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea
                            id="description"
                            wire:model="description"
                            rows="3"
                            class="mt-1 block w-full bg-surface-2 border border-border text-text placeholder-muted rounded-lg px-3 py-2 focus:border-primary focus:ring-primary"
                            placeholder="Brief description of the issue type..."
                        ></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Default Severity -->
                    <div>
                        <x-input-label for="default_severity" value="Default Severity *" />
                        <div class="mt-2 space-y-2">
                            @foreach($this->severities as $value => $label)
                                <label class="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        wire:model="default_severity"
                                        value="{{ $value }}"
                                        class="text-primary focus:ring-primary"
                                    />
                                    <span class="badge {{ $this->severityBadges[$value] }}">{{ $label }}</span>
                                    <span class="text-sm text-muted">@if($value === 'urgent') Requires immediate attention
@elseif($value === 'high') Should be addressed soon
@elseif($value === 'medium') Normal priority
@else) Can be addressed when convenient
@endif</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('default_severity')" class="mt-2" />
                    </div>

                    <!-- Issues Count (Edit mode only) -->
                    @if($isEditing && $issueType->issues()->count() > 0)
                        <div class="p-4 rounded-lg bg-warning/20 border border-warning/30">
                            <p class="text-sm text-warning">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                This issue type has {{ $issueType->issues()->count() }} associated issue(s).
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
                        {{ $isEditing ? 'Update Issue Type' : 'Create Issue Type' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
