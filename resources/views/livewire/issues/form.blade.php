<div class="max-w-3xl">
    <form wire:submit="save">
        <div class="card">
            <div class="p-6 space-y-6">
                <!-- Title -->
                <div>
                    <x-input-label for="title" value="Title *" />
                    <x-text-input
                        id="title"
                        wire:model="title"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        placeholder="Brief description of the issue"
                    />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <!-- Description -->
                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea
                        id="description"
                        wire:model="description"
                        rows="4"
                        class="mt-1 block w-full bg-surface-2 border border-border text-text placeholder-muted rounded-lg focus:border-primary focus:ring-primary"
                        placeholder="Detailed description of the issue..."
                    ></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Priority & Location -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Priority -->
                    <div>
                        <x-input-label for="priority" value="Priority *" />
                        <select
                            id="priority"
                            wire:model="priority"
                            class="mt-1 block w-full bg-surface-2 border border-border text-text rounded-lg focus:border-primary focus:ring-primary"
                            required
                        >
                            <option value="">Select priority</option>
                            @foreach($this->priorities as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                    </div>

                    <!-- Location -->
                    <div>
                        <x-input-label for="location" value="Location" />
                        <x-text-input
                            id="location"
                            wire:model="location"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g., Room 302, Front Desk, etc."
                        />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>
                </div>

                <!-- Departments -->
                <div>
                    <x-input-label for="departments" value="Departments *" />
                    <div class="mt-2 space-y-2">
                        @foreach($this->departments as $id => $name)
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    wire:model.live="department_ids"
                                    value="{{ $id }}"
                                    class="rounded border-border bg-surface-2 text-primary focus:ring-primary"
                                />
                                <span class="text-sm text-text">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('department_ids')" class="mt-2" />
                    <p class="mt-1 text-xs text-muted">Select at least one department</p>
                </div>

                <!-- Issue Types -->
                <div>
                    <x-input-label for="issue_types" value="Issue Types *" />
                    <div class="mt-2 space-y-2">
                        @foreach($this->issueTypes as $id => $name)
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    wire:model.live="issue_type_ids"
                                    value="{{ $id }}"
                                    class="rounded border-border bg-surface-2 text-primary focus:ring-primary"
                                />
                                <span class="text-sm text-text">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('issue_type_ids')" class="mt-2" />
                    <p class="mt-1 text-xs text-muted">Select at least one issue type</p>
                </div>

                <!-- Assigned To -->
                <div>
                    <x-input-label for="assigned_to" value="Assigned To" />
                    <select
                        id="assigned_to"
                        wire:model="assigned_to"
                        class="mt-1 block w-full bg-surface-2 border border-border text-text rounded-lg focus:border-primary focus:ring-primary"
                    >
                        <option value="">Unassigned</option>
                        @foreach($this->users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-surface-2 border-t border-border flex items-center justify-end gap-3">
                <button type="button" wire:click="cancel" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ $isEditing ? 'Update Issue' : 'Create Issue' }}
                </button>
            </div>
        </div>
    </form>
</div>
