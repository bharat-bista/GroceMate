<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_name',
        'category_name',
        'company_name',
        'unit',
        'qty',
        'unit_cost',
        'base_cost',
        'tax_total',
        'line_total',
        'expiry_date',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'base_cost' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'line_total' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function purchase() 
    { 
        return $this->belongsTo(Purchase::class); 
    }
    
    public function product()  
    { 
        return $this->belongsTo(Product::class); 
    }
    
    /**
     * Calculate and update totals (no taxes)
     */
    public function calculateTotals()
    {
        $this->base_cost = $this->qty * $this->unit_cost;
        $this->tax_total = 0;
        $this->line_total = $this->base_cost;
        
        return $this;
    }
}