<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends Model
{
    protected $fillable = [
    'date',
    'business_account',
    'supplier_id',
    'amount',
    'payment_method',
    'payment_reference',
    'payment_type',        // ← ADD THIS
    'bank_charge',
    'tds_applicable',
    'note',
];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'bank_charge' => 'decimal:2',
        'tds_applicable' => 'boolean',
    ];

    /**
     * Get the supplier that owns the payment.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
