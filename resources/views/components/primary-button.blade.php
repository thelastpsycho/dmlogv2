@props(['disabled' => false])

<button {{ $attributes->merge(['type' => 'submit', 'disabled' => $disabled, 'class' => 'btn btn-primary']) }}>
    {{ $slot }}
</button>
