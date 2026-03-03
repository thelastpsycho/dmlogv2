@props([
    'shortcuts' => [],
])

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('keyboard', {
            shortcuts: {{ json_encode($shortcuts) }},

            init() {
                document.addEventListener('keydown', (e) => {
                    // Ignore if user is typing in an input
                    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
                        return;
                    }

                    // Check for shortcuts
                    for (const [key, action] of Object.entries(this.shortcuts)) {
                        if (this.matchesShortcut(e, key)) {
                            e.preventDefault();
                            this.executeAction(action);
                            return;
                        }
                    }
                });
            },

            matchesShortcut(event, key) {
                // Handle special keys
                const parts = key.split('+');

                // Check for modifier keys
                if (parts.includes('cmd') || parts.includes('ctrl')) {
                    if (!event.metaKey && !event.ctrlKey) return false;
                } else {
                    if (event.metaKey || event.ctrlKey) return false;
                }

                if (parts.includes('shift')) {
                    if (!event.shiftKey) return false;
                } else {
                    if (event.shiftKey && parts.length > 1) return false;
                }

                // Get the main key (last part)
                const mainKey = parts[parts.length - 1].toLowerCase();

                // Handle special key names
                const keyMap = {
                    'escape': 'Escape',
                    'enter': 'Enter',
                    'tab': 'Tab',
                    'space': ' ',
                    'slash': '/',
                    'question': '?',
                };

                const expectedKey = keyMap[mainKey] || mainKey;

                return event.key.toLowerCase() === expectedKey.toLowerCase() ||
                       event.key === expectedKey;
            },

            executeAction(action) {
                if (typeof action === 'function') {
                    action();
                } else if (typeof action === 'string') {
                    // Navigate to URL
                    window.location.href = action;
                } else if (action.action === 'click') {
                    // Click element
                    const el = document.querySelector(action.selector);
                    if (el) el.click();
                } else if (action.action === 'focus') {
                    // Focus element
                    const el = document.querySelector(action.selector);
                    if (el) {
                        el.focus();
                        if (el.tagName === 'INPUT') {
                            el.select();
                        }
                    }
                }
            },

            formatShortcut(key) {
                const parts = key.split('+');
                const labels = parts.map(part => {
                    const labelMap = {
                        'cmd': '⌘',
                        'ctrl': 'Ctrl',
                        'shift': '⇧',
                        'escape': 'Esc',
                        'enter': 'Enter',
                        'tab': 'Tab',
                        'space': 'Space',
                        'slash': '/',
                        'question': '?',
                    };
                    return labelMap[part.toLowerCase()] || part.toUpperCase();
                });
                return labels.join(' + ');
            }
        });
    });
</script>

<!-- Help Modal (shows shortcuts) -->
<div x-data="{ open: false }"
     x-show="open"
     class="fixed inset-0 z-[100] flex items-center justify-center"
     style="display: none;"
     @keydown.escape.window="open = false">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>

    <!-- Modal -->
    <div class="relative bg-surface border border-border rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden"
         @click.away="open = false">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
            <h2 class="text-lg font-semibold text-text">Keyboard Shortcuts</h2>
            <button @click="open = false" class="text-muted hover:text-text">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
            @foreach($shortcuts as $key => $action)
                @if(is_string($action) || isset($action['label']))
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text">
                            @if(is_array($action))
                                {{ $action['label'] }}
                            @else
                                @if(str_contains($action, 'issues')) Go to Issues
                                @elseif(str_contains($action, 'reports')) Go to Reports
                                @elseif(str_contains($action, 'statistics')) Go to Statistics
                                @elseif(str_contains($action, 'dashboard')) Go to Dashboard
                                @elseif(str_contains($action, 'create')) Create New
                                @elseif(str_contains($action, 'search')) Focus Search
                                @else Navigate
                                @endif
                            @endif
                        </span>
                        <kbd class="px-2 py-1 text-xs font-mono text-muted bg-surface-2 border border-border rounded">
                            {{ $key }}
                        </kbd>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-border bg-surface-2">
            <p class="text-xs text-muted">Press <kbd class="px-1 py-0.5 text-xs font-mono bg-background border border-border rounded">?</kbd> to toggle this help</p>
        </div>
    </div>
</div>

<!-- Trigger Button -->
<button @click="$el.parentElement.querySelector('[x-data*=\"open: false\"]').open = true"
        class="hidden"
        data-keyboard-help-trigger>
</button>
