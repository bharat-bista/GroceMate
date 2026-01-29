<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_name',
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
    
    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'purchase_item_taxes')
                    ->withPivot('tax_amount')
                    ->withTimestamps();
    }
    
    /**
     * Calculate and update totals
     */
    public function calculateTotals()
    {
        $this->base_cost = $this->qty * $this->unit_cost;
        $this->tax_total = $this->taxes->sum('pivot.tax_amount');
        $this->line_total = $this->base_cost + $this->tax_total;
        
        return $this;
    }
    
    /**
     * Scope for items with taxes
     */
    public function scopeWithTaxes($query)
    {
        return $query->with('taxes');
    }
    
    /**
     * Get formatted line total
     */
    public function getFormattedLineTotalAttribute()
    {
        return number_format($this->line_total, 2);
    }
    
    /**
     * Get formatted tax total
     */
    public function getFormattedTaxTotalAttribute()
    {
        return number_format($this->tax_total, 2);
    }
    
    /**
     * Get applied taxes as string
     */
    public function getAppliedTaxesStringAttribute()
    {
        if ($this->taxes->isEmpty()) {
            return 'No Taxes';
        }
        
        return $this->taxes->map(function($tax) {
            return $tax->name . ' (' . 
                   ($tax->type === 'percentage' ? $tax->rate . '%' : $tax->rate) . 
                   ')';
        })->implode(', ');
    }
}