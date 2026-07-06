@props([
    'icon' => '📭',
    'title' => 'Nothing here yet',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center']) }}>
    <div class="text-3xl mb-2">{{ $icon }}</div>
    <p class="text-sm font-semibold text-slate-700">{{ $title }}</p>
    @if($description)
        <p class="text-sm text-slate-500 mt-1">{{ $description }}</p>
    @endif
    @isset($slot)
        @if(trim($slot))
            <div class="mt-4">{{ $slot }}</div>
        @endif
    @endisset
</div>
