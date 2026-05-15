<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'delivery_type',
        'subtotal',
        'delivery_charge',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_slip',
        'transaction_id',
        'delivery_status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refund()
    {
        return $this->hasOne(OrderRefund::class);
    }

    public function isLocked(): bool
    {
        return $this->delivery_status === 'delivered';
    }

    public function isPaymentLocked(): bool
    {
        return $this->payment_status === 'verified';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'verified';
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'GM';
        $timestamp = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        return "{$prefix}-{$timestamp}-{$random}";
    }

    public function getPaymentSlipUrlAttribute(): ?string
    {
        if (empty($this->payment_slip)) {
            return null;
        }

        if (Str::startsWith($this->payment_slip, ['http://', 'https://', 'data:'])) {
            return $this->payment_slip;
        }

        if (Str::startsWith($this->payment_slip, '/storage/')) {
            return asset(ltrim($this->payment_slip, '/'));
        }

        return asset('storage/' . ltrim($this->payment_slip, '/'));
    }

    public function getPaymentSlipIsPdfAttribute(): bool
    {
        if (empty($this->payment_slip)) {
            return false;
        }

        if (Str::startsWith($this->payment_slip, 'data:application/pdf')) {
            return true;
        }

        $path = parse_url($this->payment_slip, PHP_URL_PATH) ?: $this->payment_slip;
        return Str::endsWith(Str::lower($path), '.pdf');
    }
}
