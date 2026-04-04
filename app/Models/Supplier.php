<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'vat_number',
        'pan_number',
        'supplier_type',
        'business_account',
        'opening_due',
        'total_due',
        'address'
    ];

    protected $casts = [
        'opening_due' => 'decimal:2',
        'total_due' => 'decimal:2',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function businessAccount(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_account');
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function calculateTotalDue(): float
    {
        $openingDue = (float) ($this->opening_due ?? 0);

        $purchaseTotal = $this->relationLoaded('purchases')
            ? (float) $this->purchases->sum('total_cost')
            : (float) $this->purchases()->sum('total_cost');

        $paymentTotal = $this->relationLoaded('supplierPayments')
            ? (float) $this->supplierPayments->sum('amount')
            : (float) $this->supplierPayments()->sum('amount');

        return $openingDue + $purchaseTotal - $paymentTotal;
    }

    public function syncTotalDue(): float
    {
        $calculatedTotalDue = $this->calculateTotalDue();

        $this->forceFill(['total_due' => $calculatedTotalDue])->saveQuietly();
        $this->total_due = $calculatedTotalDue;

        return $calculatedTotalDue;
    }

    public function getCalculatedTotalDueAttribute()
    {
        return $this->calculateTotalDue();
    }
}
