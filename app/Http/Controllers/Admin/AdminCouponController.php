<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('redemptions')->latest()->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:coupons,code'],
            'type' => ['required', 'in:fixed,percent'],
            'value' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'expires_at' => $validated['expires_at'] ?? null,
            'max_uses' => $validated['max_uses'] ?? null,
            'per_user_limit' => $validated['per_user_limit'] ?? 1,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:coupons,code,' . $coupon->id],
            'type' => ['required', 'in:fixed,percent'],
            'value' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        $coupon->update([
            'code' => strtoupper(trim($validated['code'])),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'expires_at' => $validated['expires_at'] ?? null,
            'max_uses' => $validated['max_uses'] ?? null,
            'per_user_limit' => $validated['per_user_limit'] ?? 1,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
}
