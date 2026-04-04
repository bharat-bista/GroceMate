<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcommerceProductImage extends Model
{
    protected $fillable = [
        'ecommerce_product_id',
        'image_path',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function ecommerceProduct()
    {
        return $this->belongsTo(EcommerceProduct::class);
    }
}
