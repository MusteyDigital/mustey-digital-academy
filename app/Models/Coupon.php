<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'is_active',
        'expires_at',
        'max_uses',
        'per_user_limit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            return false;
        }

        if ($this->max_uses !== null && $this->redemptions()->count() >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function hasUserRedeemed(User $user): bool
    {
        if ($this->per_user_limit === null) {
            return false;
        }

        $userRedemptions = $this->redemptions()->where('user_id', $user->id)->count();

        return $userRedemptions >= $this->per_user_limit;
    }

    public function discountAmount(int $amount): int
    {
        if ($this->type === 'percent') {
            return (int) floor(($amount * $this->value) / 100);
        }

        return min($amount, (int) $this->value);
    }
}
