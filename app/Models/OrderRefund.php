<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRefund extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'refund_amount',
        'refund_status',
        'cancelled_at',
        'refunded_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
