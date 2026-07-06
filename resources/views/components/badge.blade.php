@props([
    'variant' => 'neutral', // neutral, primary, success, warning, danger
    'border' => false,
])

@php
    $variants = [
        'neutral' => ['bg-slate-100 text-slate-700', 'border-slate-200'],
        'primary' => ['bg-primary-100 text-primary-800', 'border-primary-200'],
        'success' => ['bg-success-100 text-success-700', 'border-success-200'],
        'warning' => ['bg-warning-100 text-warning-700', 'border-warning-200'],
        'danger'  => ['bg-danger-100 text-danger-700', 'border-danger-200'],
    ];

    [$colorClasses, $borderClass] = $variants[$variant] ?? $variants['neutral'];
    $borderClasses = $border ? "border $borderClass" : '';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold $colorClasses $borderClasses"]) }}>
    {{ $slot }}
</span>
