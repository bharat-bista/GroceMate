<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EcommerceProduct extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'status',
        'display_section',
        'ecommerce_stock',
        'previous_price',
        'mrp',
        'discount_percent',
        'display_price',
        'profit',
        'meta_keywords',
        'description',
        'thumbnail',
    ];

    protected $casts = [
        'ecommerce_stock' => 'decimal:3',
        'previous_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'display_price' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(EcommerceProductImage::class);
    }

    public function scopeLatestPerProduct(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();
        $qualifiedId = $query->getModel()->qualifyColumn('id');

        return $query->whereIn($qualifiedId, function ($subQuery) use ($table) {
            $subQuery->from($table)
                ->selectRaw('MAX(id)')
                ->groupBy('product_id');
        });
    }

    // Calculate display price from MRP and discount
    public function calculateDisplayPrice()
    {
        if ($this->discount_percent > 0) {
            return $this->mrp - ($this->mrp * $this->discount_percent / 100);
        }
        return $this->mrp;
    }

    // Calculate profit (display_price - purchase_price from inventory)
    public function calculateProfit()
    {
        $purchasePrice = $this->product->latestPurchaseItem->unit_cost ?? 0;
        return $this->display_price - $purchasePrice;
    }
}
