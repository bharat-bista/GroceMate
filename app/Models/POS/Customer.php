<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'email',
        'vat_number',
        'pan_number',
        'customer_type',
        'opening_due',
        'total_due',
        'address',
        'notes'
    ];

    protected $casts = [
        'opening_due' => 'integer',
        'total_due' => 'integer',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function calculateTotalDue(): float
    {
        $openingDue = (float) ($this->opening_due ?? 0);

        $creditInvoiceTotal = $this->relationLoaded('invoices')
            ? (float) $this->invoices->where('payment_method', 'credit')->sum('total_cost')
            : (float) $this->invoices()->where('payment_method', 'credit')->sum('total_cost');

        $paymentTotal = $this->relationLoaded('incomes')
            ? (float) $this->incomes->where('amount_received', '>', 0)->sum('amount_received')
            : (float) $this->incomes()->where('amount_received', '>', 0)->sum('amount_received');

        return max(0, $openingDue + $creditInvoiceTotal - $paymentTotal);
    }

    public function syncTotalDue(): float
    {
        $calculatedTotalDue = $this->calculateTotalDue();

        $this->forceFill(['total_due' => $calculatedTotalDue])->saveQuietly();
        $this->total_due = $calculatedTotalDue;

        return $calculatedTotalDue;
    }

    public function getCalculatedTotalDueAttribute(): float
    {
        return $this->calculateTotalDue();
    }
}
