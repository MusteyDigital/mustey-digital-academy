@props([
    'variant' => 'neutral', // neutral, primary, success, warning, danger
])

@php
    $variants = [
        'neutral' => 'bg-slate-100 text-slate-700',
        'primary' => 'bg-primary-100 text-primary-800',
        'success' => 'bg-success-100 text-success-700',
        'warning' => 'bg-warning-100 text-warning-700',
        'danger'  => 'bg-danger-100 text-danger-700',
    ];

    $classes = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold $classes"]) }}>
    {{ $slot }}
</span>
