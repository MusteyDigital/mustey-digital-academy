@props([
    'label',
    'value',
    'color' => 'primary', // primary, accent, success, warning, danger, or any tailwind color prefix like 'purple'
])

@php
    $colorMap = [
        'primary' => 'bg-primary-50 text-primary-600',
        'accent'  => 'bg-accent-50 text-accent-600',
        'success' => 'bg-success-50 text-success-600',
        'warning' => 'bg-warning-50 text-warning-600',
        'danger'  => 'bg-danger-50 text-danger-600',
    ];

    $iconClasses = $colorMap[$color] ?? "bg-{$color}-50 text-{$color}-600";
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-5']) }}>
    <div class="w-9 h-9 rounded-xl {{ $iconClasses }} flex items-center justify-center mb-3">
        {{ $slot }}
    </div>
    <div class="text-sm text-slate-500">{{ $label }}</div>
    <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ $value }}</div>
</div>
