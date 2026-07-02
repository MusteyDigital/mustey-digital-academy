@props(['disabled' => false])
<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm']) }}>