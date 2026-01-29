<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = ['name', 'type', 'rate'];
    
    protected $casts = [
        'rate' => 'decimal:2',
    ];
    
    public function purchaseItems()
    {
        return $this->belongsToMany(PurchaseItem::class, 'purchase_item_taxes')
                    ->withPivot('tax_amount')
                    ->withTimestamps();
    }
    
    /**
     * Get formatted rate
     */
    public function getFormattedRateAttribute()
    {
        return $this->type === 'percentage' 
            ? $this->rate . '%' 
            : number_format($this->rate, 2);
    }
}