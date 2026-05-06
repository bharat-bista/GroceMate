<?php

namespace App\Models\POS;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'unit',
        'qty',
        'unit_cost',
        'base_cost',
        'tax_total',
        'line_total',
        'expiry_date',
        'batches_consumed',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'base_cost' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'line_total' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
