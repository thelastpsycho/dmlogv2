@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-surface-2 border border-border text-text placeholder-muted focus:border-primary focus:ring-primary rounded-md shadow-sm']) }}>
