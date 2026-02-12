<?php

namespace App\Models\POS;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'created_by',
        'purchase_date',
        'invoice_no',
        'total_cost',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
