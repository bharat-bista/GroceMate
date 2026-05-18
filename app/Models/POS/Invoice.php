<?php

namespace App\Models\POS;

use App\Models\POS\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'business_id',
        'created_by',
        'invoice_date',
        'invoice_no',
        'total_cost',
        'discount',
        'payment_method',
        'status',
        'cancellation_status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total_cost' => 'integer',
        'discount'   => 'integer',
        'cancelled_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the total amount paid for this invoice
     */
    public function getTotalPaidAttribute()
    {
        // Try multiple patterns to find payments for this invoice
        $patterns = [
            '%Invoice #' . $this->invoice_no . '%',
            '%INV-' . str_replace('INV-', '', $this->invoice_no) . '%',
            '%' . str_replace('INV-', '', $this->invoice_no) . '%',
        ];
        
        $totalPaid = 0;
        foreach ($patterns as $pattern) {
            $paid = \App\Models\POS\Income::where('customer_id', $this->customer_id)
                ->where('income_type', 'Due Collection')
                ->where('description', 'like', $pattern)
                ->sum('amount_received');
            $totalPaid += $paid;
        }
        
        return $totalPaid;
    }

    /**
     * Get the remaining due amount
     */
    public function getRemainingDueAttribute()
    {
        return $this->total_cost - $this->total_paid;
    }

    /**
     * Check if invoice is fully paid
     */
    public function isFullyPaid()
    {
        return $this->remaining_due <= 0;
    }

    /**
     * Update invoice status based on payments
     */
    public function updateStatus()
    {
        if ($this->isFullyPaid()) {
            $this->status = 'Complete';
        } else {
            $this->status = 'Pending';
        }
        // Don't save here - let the calling method handle saving
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->status === 'Complete') {
            return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Complete</span>';
        } else {
            return '<span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Pending</span>';
        }
    }

    public function business()
    {
        return $this->belongsTo(\App\Models\Business::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
