<x-layouts.admin>
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
        <h3 class="font-semibold text-gray-800 text-lg mb-4">Create Coupon</h3>

        <form method="POST" action="{{ route('admin.coupons.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1">Code</label>
                <input type="text" name="code" value="{{ old('code') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                       placeholder="DATA50">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Type</label>
                <select name="type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="fixed">Fixed</option>
                    <option value="percent">Percent</option>
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Value</label>
                <input type="number" name="value" value="{{ old('value') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                       placeholder="50">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Expires At</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-end gap-3">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="text-sm text-gray-700">Active</span>
                </label>

                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6 border mt-6">
        <h3 class="font-semibold text-gray-800 text-lg mb-4">Coupons</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left p-3">Code</th>
                        <th class="text-left p-3">Type</th>
                        <th class="text-left p-3">Value</th>
                        <th class="text-left p-3">Status</th>
                        <th class="text-left p-3">Expires</th>
                        <th class="text-left p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr class="border-b align-top">
                            <td class="p-3 font-semibold">{{ $coupon->code }}</td>
                            <td class="p-3 uppercase">{{ $coupon->type }}</td>
                            <td class="p-3">
                                @if($coupon->type === 'percent')
                                    {{ $coupon->value }}%
                                @else
                                    ₦{{ number_format($coupon->value) }}
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $coupon->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </td>
                            <td class="p-3">
                                {{ $coupon->expires_at ? $coupon->expires_at->format('M j, Y g:i A') : 'No expiry' }}
                            </td>
                            <td class="p-3">
                                <details>
                                    <summary class="cursor-pointer text-blue-600">Edit</summary>

                                    <div class="mt-3 space-y-3 min-w-[280px]">
                                        <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')

                                            <input type="text" name="code" value="{{ $coupon->code }}"
                                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                                            <select name="type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                                <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                <option value="percent" {{ $coupon->type === 'percent' ? 'selected' : '' }}>Percent</option>
                                            </select>

                                            <input type="number" name="value" value="{{ $coupon->value }}"
                                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                                            <input type="datetime-local" name="expires_at"
                                                   value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\\TH:i') : '' }}"
                                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                                            <label class="inline-flex items-center gap-2">
                                                <input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-700">Active</span>
                                            </label>

                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                Update
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-gray-600">No coupons found.</td>
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
