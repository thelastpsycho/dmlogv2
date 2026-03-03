@props(['disabled' => false])

<button {{ $attributes->merge(['type' => 'button', 'disabled' => $disabled, 'class' => 'btn btn-secondary']) }}>
    {{ $slot }}
</button>
