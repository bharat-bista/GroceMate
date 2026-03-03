<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\POS\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'customer_id',
        'created_by',
        'transaction_date',
        'amount_received',
        'payment_method',
        'income_type',
        'description',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount_received' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the income.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created the income.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rs ' . number_format($this->amount_received, 2);
    }

    /**
     * Get auto-generated reference number.
     */
    public function getAutoReferenceAttribute(): string
    {
        return 'INC-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
