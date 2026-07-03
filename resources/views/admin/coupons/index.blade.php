<x-layouts.admin>
    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Create Coupon --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-semibold text-slate-800 text-lg mb-4">Create Coupon</h3>

        <form method="POST" action="{{ route('admin.coupons.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @csrf

            <div>
                <label class="text-sm font-medium text-slate-600 mb-1 block">Code</label>
                <input type="text" name="code" value="{{ old('code') }}" required
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="DATA50">
            </div>

            <div>
                <label class="text-sm font-medium text-slate-600 mb-1 block">Type</label>
                <select name="type" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="fixed">Fixed</option>
                    <option value="percent">Percent</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-slate-600 mb-1 block">Value</label>
                <input type="number" name="value" value="{{ old('value') }}" required min="0" step="0.01"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="50">
            </div>

            <div>
                <label class="text-sm font-medium text-slate-600 mb-1 block">Expires At</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div class="flex items-end gap-4">
                <label class="inline-flex items-center gap-2 pb-2.5">
                    <input type="checkbox" name="is_active" value="1" checked
                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-slate-700">Active</span>
                </label>

                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                    Create
                </button>
            </div>
        </form>
    </div>

    {{-- Coupons Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mt-6">
        <h3 class="font-semibold text-slate-800 text-lg mb-4">Coupons</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Code</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Type</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Value</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Status</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Expires</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr class="border-b border-slate-100 last:border-b-0 align-top">
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $coupon->code }}</td>
                            <td class="py-3 px-4 uppercase text-slate-600 text-xs font-medium">{{ $coupon->type }}</td>
                            <td class="py-3 px-4 text-slate-700">
                                @if($coupon->type === 'percent')
                                    {{ $coupon->value }}%
                                @else
                                    ₦{{ number_format($coupon->value) }}
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $coupon->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $coupon->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $coupon->expires_at ? $coupon->expires_at->format('M j, Y g:i A') : 'No expiry' }}
                            </td>
                            <td class="py-3 px-4">
                                <details class="group">
                                    <summary class="cursor-pointer text-blue-600 font-medium text-sm hover:text-blue-700 transition list-none inline-flex items-center gap-1">
                                        Edit
                                        <svg class="w-3.5 h-3.5 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </summary>

                                    <div class="mt-4 space-y-3 min-w-[280px] bg-slate-50 border border-slate-200 rounded-xl p-4">
                                        <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')

                                            <input type="text" name="code" value="{{ $coupon->code }}" required
                                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">

                                            <select name="type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                                <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                <option value="percent" {{ $coupon->type === 'percent' ? 'selected' : '' }}>Percent</option>
                                            </select>

                                            <input type="number" name="value" value="{{ $coupon->value }}" required min="0" step="0.01"
                                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">

                                            <input type="datetime-local" name="expires_at"
                                                   value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\\TH:i') : '' }}"
                                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">

                                            <label class="inline-flex items-center gap-2">
                                                <input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }}
                                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                <span class="text-sm text-slate-700">Active</span>
                                            </label>

                                            <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                                                Update
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}"
                                              onsubmit="return confirm('Delete coupon &quot;{{ $coupon->code }}&quot;? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 px-4 text-slate-500 text-center">No coupons found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    </div>
</x-layouts.admin>