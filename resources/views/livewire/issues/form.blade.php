<div class="max-w-4xl">
    <form wire:submit="save">
        <div class="card">
            <div class="p-4 space-y-6">
                <!-- Issue Details Section -->
                <div>
                    <h3 class="text-base font-medium text-text mb-4 flex items-center gap-2">
                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Issue Details
                    </h3>
                    <div class="space-y-4">
                        <!-- Title -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text">Title <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                wire:model="title"
                                class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/60"
                                required
                                autofocus
                                placeholder="Brief description of the issue"
                            />
                            <x-input-error :messages="$errors->get('title')" />
                        </div>

                        <!-- Description -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text">Description</label>

                            <div class="relative">
                                <textarea
                                    wire:model="description"
                                    wire:key="description-{{ $description ?? 'empty' }}"
                                    rows="3"
                                    class="w-full pr-24 bg-surface border border-border text-text placeholder:text-muted/60 rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none"
                                    placeholder="Detailed description of the issue..."
                                >{{ $description }}</textarea>

                                <!-- AI Button -->
                                @if($this->aiAvailable && !$aiLoading)
                                    <button
                                        type="button"
                                        wire:click="assistWithAI"
                                        wire:loading.attr="disabled"
                                        class="absolute top-2 right-2 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                        <span>Enhance</span>
                                    </button>
                                @elseif($aiLoading)
                                    <button
                                        disabled
                                        class="absolute top-2 right-2 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-primary rounded-lg opacity-75"
                                    >
                                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Enhancing...</span>
                                    </button>
                                @else
                                    <button
                                        disabled
                                        class="absolute top-2 right-2 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-muted bg-surface-2 border border-border rounded-lg cursor-not-allowed"
                                        title="{{ $this->aiError ? '⚠️ ' . $this->aiError : 'AI service unavailable' }}"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>Unavailable</span>
                                    </button>

                                    <!-- Retry countdown -->
                                    @if($this->aiRetryAfter)
                                        <button
                                            type="button"
                                            wire:click="retryAI"
                                            class="absolute top-10 right-2 text-xs text-primary hover:underline"
                                        >
                                            Retry in: {{ $this->aiRetryAfter }}
                                        </button>
                                    @endif
                                @endif
                            </div>

                            <x-input-error :messages="$errors->get('description')" />

                            <!-- Flash messages -->
                            @session('ai_success')
                                <div class="mt-2 text-sm text-success flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $value }}
                                </div>
                            @endsession

                            @session('ai_error')
                                <div class="mt-2 text-sm text-danger flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $value }}
                                </div>
                            @endsession
                        </div>

                        <!-- Recovery Action -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Recovery Action Taken
                            </label>
                            <textarea
                                wire:model="recovery"
                                rows="2"
                                class="w-full bg-surface border border-border text-text placeholder:text-muted/60 rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none"
                                placeholder="Describe the recovery actions taken..."
                            ></textarea>
                            <x-input-error :messages="$errors->get('recovery')" />
                        </div>

                        <!-- Priority & Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Priority -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-text">Priority <span class="text-danger">*</span></label>
                                <div class="relative">
                                    <select
                                        wire:model="priority"
                                        class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all appearance-none cursor-pointer"
                                        required
                                    >
                                        <option value="">Select priority</option>
                                        @foreach($this->priorities as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="w-4 h-4 text-muted absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                <x-input-error :messages="$errors->get('priority')" />
                            </div>

                            <!-- Location -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-text">Location</label>
                                <input
                                    type="text"
                                    wire:model="location"
                                    class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/60"
                                    placeholder="e.g., Room 302, Front Desk"
                                />
                                <x-input-error :messages="$errors->get('location')" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guest Details Section -->
                <div>
                    <h3 class="text-base font-medium text-text mb-4 flex items-center gap-2">
                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Guest Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Guest Name -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text">Guest Name</label>
                            <input
                                type="text"
                                wire:model="name"
                                class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/60"
                                placeholder="Guest name"
                            />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <!-- Room Number -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text">Room Number</label>
                            <input
                                type="text"
                                wire:model="room_number"
                                class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/60"
                                placeholder="e.g., 302"
                            />
                            <x-input-error :messages="$errors->get('room_number')" />
                        </div>

                        <!-- Nationality -->
                        <div class="space-y-1.5" x-data="nationalitySelect()" x-init="initSelect(@js($nationality))">
                            <span class="hidden" data-nationalities="{{ implode(',', array_values($this->nationalities)) }}"></span>
                            <label class="text-sm font-medium text-text">Nationality</label>
                            <div class="relative">
                                <button
                                    type="button"
                                    @click="toggleDropdown()"
                                    class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 text-left flex items-center justify-between focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all hover:border-border/80"
                                    :class="{'border-primary ring-2 ring-primary/20': isOpen}"
                                >
                                    <span class="truncate" :class="{'text-muted/60': !selected}" x-text="selected || 'Select nationality'"></span>
                                    <div class="flex items-center gap-1">
                                        <template x-if="selected">
                                            <button @click.stop="clear()" class="p-1 text-muted/60 hover:text-text hover:bg-surface-2 rounded transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </template>
                                        <svg class="w-4 h-4 text-muted transition-transform duration-200" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </button>

                                <div
                                    x-show="isOpen"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                    @click.outside="closeDropdown()"
                                    class="absolute z-50 mt-2 w-full bg-surface border border-border rounded-xl shadow-xl shadow-black/10 overflow-hidden"
                                    style="display: none;"
                                >
                                    <div class="p-2.5 border-b border-border bg-surface-2/50">
                                        <div class="relative">
                                            <svg class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <input
                                                type="text"
                                                x-model="search"
                                                placeholder="Search nationalities..."
                                                class="w-full pl-10 pr-4 py-2 text-sm bg-surface border border-border rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/50"
                                            >
                                        </div>
                                    </div>

                                    <div class="max-h-56 overflow-y-auto p-1.5 custom-scrollbar">
                                        <template x-for="option in filteredOptions">
                                            <button
                                                type="button"
                                                @click="select(option)"
                                                class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-all duration-150"
                                                :class="option === selected ? 'bg-primary text-white font-medium shadow-sm' : 'text-text hover:bg-surface-2'"
                                                x-text="option"
                                            ></button>
                                        </template>
                                        <div x-show="filteredOptions.length === 0" class="px-3 py-6 text-center text-sm text-muted" style="display: none;">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-muted/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            No nationalities found
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('nationality')" />
                        </div>

                        <!-- Contact -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text">Contact</label>
                            <input
                                type="text"
                                wire:model="contact"
                                class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/60"
                                placeholder="Phone or email"
                            />
                            <x-input-error :messages="$errors->get('contact')" />
                        </div>

                    </div>

                    <!-- Date Row -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-4">
                        <!-- Check-in / Check-out Date Range -->
                        <div class="space-y-1.5 flex-1 min-w-0" x-data="dateRangePicker({ startDate: @js($checkin_date), endDate: @js($checkout_date), wirePropertyStart: 'checkin_date', wirePropertyEnd: 'checkout_date' })">
                            <label class="text-sm font-medium text-text flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Check-in / Check-out <span class="text-danger">*</span>
                            </label>
                            <div class="relative">
                                <input type="hidden" :value="startDate">
                                <input type="hidden" :value="endDate">
                                <button
                                    type="button"
                                    @click="open()"
                                    class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 text-left flex items-center justify-between focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all hover:border-border/80"
                                    :class="{'border-primary ring-2 ring-primary/20': isOpen}"
                                >
                                    <span :class="{'text-muted/60': !startDate && !endDate}" x-text="formattedRange"></span>
                                    <svg class="w-4 h-4 text-muted transition-transform" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="isOpen"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                    @click.outside="close()"
                                    class="absolute z-50 mt-2 w-full bg-surface border border-border rounded-xl shadow-xl shadow-black/10 overflow-hidden"
                                    style="display: none;"
                                >
                                    <!-- Header with navigation -->
                                    <div class="flex items-center justify-between px-4 py-3 border-b border-border">
                                        <button type="button" @click="prevMonths()" class="p-1.5 hover:bg-surface-2 rounded-lg transition-colors" title="Previous months">
                                            <svg class="w-4 h-4 text-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <span class="text-sm font-semibold text-text">
                                            <span x-text="monthNames[firstMonth.getMonth()]"></span>
                                            <span class="text-muted/60 mx-1">–</span>
                                            <span x-text="monthNames[secondMonth.getMonth()]"></span>
                                            <span class="text-muted/60 ml-1" x-text="firstMonth.getFullYear()"></span>
                                        </span>
                                        <button type="button" @click="nextMonths()" class="p-1.5 hover:bg-surface-2 rounded-lg transition-colors" title="Next months">
                                            <svg class="w-4 h-4 text-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="p-4">
                                        <div class="flex gap-4">
                                        <template x-for="monthIdx in [0, 1]">
                                            <div class="flex-1">
                                                <div class="text-center text-sm font-medium text-text mb-3" x-text="monthNames[(firstMonth.getMonth() + monthIdx) % 12] + ' ' + (firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12))"></div>
                                                <div class="grid grid-cols-7 gap-0.5 mb-1">
                                                    <template x-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']">
                                                        <div class="text-center text-xs text-muted py-1" x-text="day"></div>
                                                    </template>
                                                </div>
                                                <div class="grid grid-cols-7 gap-0.5">
                                                    <template x-for="day in getCalendarDays((firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12)), (firstMonth.getMonth() + monthIdx) % 12)">
                                                        <button
                                                            type="button"
                                                            :disabled="!day"
                                                            @click="day && selectDate((firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12)), (firstMonth.getMonth() + monthIdx) % 12, day)"
                                                            @mouseenter="day && setHovered((firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12)), (firstMonth.getMonth() + monthIdx) % 12, day)"
                                                            @mouseleave="day && clearHovered()"
                                                            class="aspect-square text-xs rounded-lg transition-all"
                                                            :class="{
                                                                'invisible': !day,
                                                                'bg-primary text-white shadow-sm': isSelected((firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12)), (firstMonth.getMonth() + monthIdx) % 12, day) === 'start' || isSelected((firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12)), (firstMonth.getMonth() + monthIdx) % 12, day) === 'end',
                                                                'bg-primary/15 text-primary': isInRangeOrHovered((firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12)), (firstMonth.getMonth() + monthIdx) % 12, day),
                                                                'hover:bg-surface-2 text-text': day && !isSelected((firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12)), (firstMonth.getMonth() + monthIdx) % 12, day) && !isInRangeOrHovered((firstMonth.getFullYear() + Math.floor((firstMonth.getMonth() + monthIdx) / 12)), (firstMonth.getMonth() + monthIdx) % 12, day)
                                                            }"
                                                            x-text="day || ''"
                                                        ></button>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    </div>

                                    <div class="px-4 py-3 border-t border-border flex items-center justify-between">
                                        <div class="text-sm text-text">
                                            <span x-show="startDate" class="text-primary font-medium" x-text="startDate ? new Date(startDate + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : ''"></span>
                                            <span x-show="startDate && endDate" class="text-muted mx-1.5">→</span>
                                            <span x-show="endDate" class="text-primary font-medium" x-text="endDate ? new Date(endDate + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : ''"></span>
                                            <span x-show="!startDate && !endDate" class="text-muted/60 text-xs">Select dates</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" @click="clear(); close()" class="text-xs text-muted hover:text-text px-3 py-1.5 rounded-lg hover:bg-surface-2 transition-colors">Clear</button>
                                            <button type="button" @click="close()" class="text-xs bg-primary text-white px-3 py-1.5 rounded-lg hover:bg-primary/90 transition-colors">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <x-input-error :messages="$errors->get('checkin_date')" />
                                <x-input-error :messages="$errors->get('checkout_date')" />
                            </div>
                        </div>

                        <!-- Issue Date -->
                        <div class="space-y-1.5 flex-1 min-w-0" x-data="datePicker({ value: @js($issue_date), wireProperty: 'issue_date' })">
                            <label class="text-sm font-medium text-text flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Issue Date <span class="text-danger">*</span>
                            </label>
                            <div class="relative">
                                <input type="hidden" :value="value">
                                <button
                                    type="button"
                                    @click="open()"
                                    class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 text-left flex items-center justify-between focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all hover:border-border/80"
                                    :class="{'border-primary ring-2 ring-primary/20': isOpen}"
                                >
                                    <span :class="{'text-muted/60': !value}" x-text="formattedValue || 'Select date'"></span>
                                    <svg class="w-4 h-4 text-muted transition-transform" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="isOpen"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                    @click.outside="close()"
                                    class="absolute z-50 mt-2 w-full bg-surface border border-border rounded-xl shadow-xl shadow-black/10 overflow-hidden"
                                    style="display: none;"
                                >
                                    <div class="flex items-center justify-between px-4 py-3 border-b border-border">
                                        <button type="button" @click="prevMonth()" class="p-1 hover:bg-surface-2 rounded-lg transition-colors">
                                            <svg class="w-4 h-4 text-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <span class="text-sm font-medium text-text" x-text="monthNames[selectedMonth] + ' ' + selectedYear"></span>
                                        <button type="button" @click="nextMonth()" class="p-1 hover:bg-surface-2 rounded-lg transition-colors">
                                            <svg class="w-4 h-4 text-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-7 gap-0.5 px-2 pt-2">
                                        <template x-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']">
                                            <div class="text-center text-xs text-muted py-1" x-text="day"></div>
                                        </template>
                                    </div>

                                    <div class="grid grid-cols-7 gap-0.5 px-2 pb-2">
                                        <template x-for="day in calendarDays">
                                            <button
                                                type="button"
                                                :disabled="!day"
                                                @click="day && selectDay(day)"
                                                class="aspect-square text-sm rounded-lg transition-all"
                                                :class="{
                                                    'invisible': !day,
                                                    'bg-primary text-white shadow-sm': isSelected(day),
                                                    'bg-primary/15 text-primary font-medium': isToday(day) && !isSelected(day),
                                                    'hover:bg-surface-2 text-text': day && !isSelected(day) && !isToday(day)
                                                }"
                                                x-text="day || ''"
                                            ></button>
                                        </template>
                                    </div>

                                    <div class="px-4 py-2 border-t border-border flex justify-between">
                                        <button type="button" @click="clear()" class="text-xs text-muted hover:text-text">Clear</button>
                                        <button type="button" @click="selectDay(today.getDate()); selectedMonth = today.getMonth(); selectedYear = today.getFullYear()" class="text-xs text-primary hover:underline">Today</button>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('issue_date')" />
                        </div>
                    </div>

                    <!-- Additional Details Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <!-- Source -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text">Source</label>
                            <input
                                type="text"
                                wire:model="source"
                                class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/60"
                                placeholder="OTA, Direct, etc."
                            />
                            <x-input-error :messages="$errors->get('source')" />
                        </div>

                        <!-- Recovery Cost -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text">Recovery Cost</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted/60 text-sm">$</span>
                                <input
                                    type="number"
                                    wire:model="recovery_cost"
                                    min="0"
                                    step="1"
                                    class="w-full bg-surface border border-border text-text rounded-lg pl-7 pr-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/60"
                                    placeholder="0.00"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('recovery_cost')" />
                        </div>

                        <!-- Training Notes Indicator -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-text">Training Needed</label>
                            <div class="flex items-center h-[42px] px-3 py-2.5 rounded-lg border border-dashed border-border text-muted/60 text-sm">
                                <svg class="w-4 h-4 mr-2 text-muted/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                See training field below
                            </div>
                        </div>
                    </div>

                    <!-- Training (full width) -->
                    <div class="mt-4 space-y-1.5">
                        <label class="text-sm font-medium text-text flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Training Required
                        </label>
                        <textarea
                            wire:model="training"
                            rows="2"
                            class="w-full bg-surface border border-border text-text placeholder:text-muted/60 rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none"
                            placeholder="Describe any training needed to prevent similar issues..."
                        ></textarea>
                        <x-input-error :messages="$errors->get('training')" class="mt-2" />
                    </div>
                </div>

                <!-- Classification Section -->
                <div>
                    <h3 class="text-base font-medium text-text mb-4 flex items-center gap-2">
                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Classification
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Departments Multi-Select -->
                        <div class="space-y-1.5" x-data="multiSelect({
                            selected: @js($department_ids),
                            options: @js(collect($this->departments)->map(fn($name, $id) => ['id' => (string)$id, 'name' => $name])->values()->toArray()),
                            wireProperty: 'department_ids'
                        })" x-init="init()" wire:key="departments-{{ implode(',', $department_ids) }}">
                            <label class="text-sm font-medium text-text">Departments <span class="text-danger">*</span></label>
                            <div class="relative">
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="department_ids[]" :value="id">
                                </template>

                                <button
                                    type="button"
                                    @click="toggleDropdown()"
                                    class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 text-left flex items-center justify-between focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all hover:border-border/80"
                                    :class="{'border-primary ring-2 ring-primary/20': isOpen}"
                                >
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <template x-for="id in selected" :key="id">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-primary bg-primary/10 rounded-full">
                                                <span x-text="options.find(o => o.id === id)?.name"></span>
                                                <button
                                                    type="button"
                                                    @click.stop="selected = selected.filter(s => s !== id)"
                                                    class="hover:text-primary/70 focus:outline-none"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <span x-show="selected.length === 0" class="text-muted/60">Select departments</span>
                                    </div>
                                    <svg class="w-4 h-4 text-muted transition-transform flex-shrink-0" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="isOpen"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                    @click.outside="closeDropdown()"
                                    class="absolute z-50 mt-2 w-full bg-surface border border-border rounded-xl shadow-xl shadow-black/10 overflow-hidden"
                                    style="display: none;"
                                >
                                    <div class="p-2.5 border-b border-border bg-surface-2/50">
                                        <div class="relative">
                                            <svg class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <input
                                                type="text"
                                                x-model="search"
                                                placeholder="Search departments..."
                                                class="w-full pl-10 pr-4 py-2 text-sm bg-surface border border-border rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/50"
                                                @click="$event.stopPropagation()"
                                            >
                                        </div>
                                    </div>

                                    <div class="max-h-56 overflow-y-auto p-1.5 custom-scrollbar">
                                        <template x-for="option in filteredOptions" :key="option.id">
                                            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-surface-2 rounded-lg cursor-pointer transition-colors">
                                                <input
                                                    type="checkbox"
                                                    :value="option.id"
                                                    x-model="selected"
                                                    class="w-4 h-4 rounded border-border text-primary focus:ring-2 focus:ring-primary/20"
                                                >
                                                <span class="text-sm text-text" x-text="option.name"></span>
                                            </label>
                                        </template>
                                        <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-sm text-muted" style="display: none;">
                                            No departments found
                                        </div>
                                    </div>

                                    <div class="p-2.5 border-t border-border flex items-center justify-between bg-surface-2/30">
                                        <button
                                            type="button"
                                            @click="selectAll()"
                                            class="text-xs text-primary hover:underline transition-colors"
                                            x-text="selected.length === filteredOptions.length ? 'Deselect All' : 'Select All'"
                                        ></button>
                                        <span class="text-xs text-muted" x-text="`${selected.length} selected`"></span>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('department_ids')" />
                        </div>
                        <!-- Issue Types Multi-Select -->
                        <div class="space-y-1.5" x-data="multiSelect({
                            selected: @js($issue_type_ids),
                            options: @js(collect($this->issueTypes)->map(fn($name, $id) => ['id' => (string)$id, 'name' => $name])->values()->toArray()),
                            wireProperty: 'issue_type_ids'
                        })" x-init="init()" wire:key="issue-types-{{ implode(',', $issue_type_ids) }}">
                            <label class="text-sm font-medium text-text">Issue Types <span class="text-danger">*</span></label>
                            <div class="relative">
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="issue_type_ids[]" :value="id">
                                </template>

                                <button
                                    type="button"
                                    @click="toggleDropdown()"
                                    class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 text-left flex items-center justify-between focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all hover:border-border/80"
                                    :class="{'border-primary ring-2 ring-primary/20': isOpen}"
                                >
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <template x-for="id in selected" :key="id">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-primary bg-primary/10 rounded-full">
                                                <span x-text="options.find(o => o.id === id)?.name"></span>
                                                <button
                                                    type="button"
                                                    @click.stop="selected = selected.filter(s => s !== id)"
                                                    class="hover:text-primary/70 focus:outline-none"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <span x-show="selected.length === 0" class="text-muted/60">Select issue types</span>
                                    </div>
                                    <svg class="w-4 h-4 text-muted transition-transform flex-shrink-0" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="isOpen"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                    @click.outside="closeDropdown()"
                                    class="absolute z-50 mt-2 w-full bg-surface border border-border rounded-xl shadow-xl shadow-black/10 overflow-hidden"
                                    style="display: none;"
                                >
                                    <div class="p-2.5 border-b border-border bg-surface-2/50">
                                        <div class="relative">
                                            <svg class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <input
                                                type="text"
                                                x-model="search"
                                                placeholder="Search issue types..."
                                                class="w-full pl-10 pr-4 py-2 text-sm bg-surface border border-border rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-muted/50"
                                                @click="$event.stopPropagation()"
                                            >
                                        </div>
                                    </div>

                                    <div class="max-h-56 overflow-y-auto p-1.5 custom-scrollbar">
                                        <template x-for="option in filteredOptions" :key="option.id">
                                            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-surface-2 rounded-lg cursor-pointer transition-colors">
                                                <input
                                                    type="checkbox"
                                                    :value="option.id"
                                                    x-model="selected"
                                                    class="w-4 h-4 rounded border-border text-primary focus:ring-2 focus:ring-primary/20"
                                                >
                                                <span class="text-sm text-text" x-text="option.name"></span>
                                            </label>
                                        </template>
                                        <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-sm text-muted" style="display: none;">
                                            No issue types found
                                        </div>
                                    </div>

                                    <div class="p-2.5 border-t border-border flex items-center justify-between bg-surface-2/30">
                                        <button
                                            type="button"
                                            @click="selectAll()"
                                            class="text-xs text-primary hover:underline transition-colors"
                                            x-text="selected.length === filteredOptions.length ? 'Deselect All' : 'Select All'"
                                        ></button>
                                        <span class="text-xs text-muted" x-text="`${selected.length} selected`"></span>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('issue_type_ids')" />
                        </div>
                    </div>
                </div>

                <!-- Assignment Section -->
                <div>
                    <h3 class="text-base font-medium text-text mb-4 flex items-center gap-2">
                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        Assignment
                    </h3>
                    <div class="space-y-4">
                        <!-- Assigned To -->
                        <div class="space-y-1.5 max-w-md">
                            <label class="text-sm font-medium text-text flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Assigned To
                            </label>
                            <div class="relative">
                                <select
                                    wire:model="assigned_to"
                                    class="w-full bg-surface border border-border text-text rounded-lg px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all appearance-none cursor-pointer"
                                >
                                    <option value="">Unassigned</option>
                                    @foreach($this->users as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <svg class="w-4 h-4 text-muted absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <x-input-error :messages="$errors->get('assigned_to')" />
                            <p class="text-xs text-muted/60">Optional: Assign this issue to a team member</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-4 py-3 bg-surface-2/50 border-t border-border flex items-center justify-end gap-3">
                <button type="button" wire:click="cancel" class="px-4 py-2.5 text-sm font-medium text-text border border-border rounded-lg hover:bg-surface-2 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
                    {{ $isEditing ? 'Update Issue' : 'Create Issue' }}
                </button>
            </div>
        </div>
    </form>
</div>
