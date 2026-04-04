<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'image', 'order'];

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    // Get products that are listed in e-commerce
    public function ecommerceProducts()
    {
        return $this->hasManyThrough(
            \App\Models\EcommerceProduct::class,
            \App\Models\Product::class,
            'category_id',
            'product_id',
            'id',
            'id'
        );
    }

    // Check if category has any e-commerce products
    public function hasEcommerceProducts()
    {
        return $this->ecommerceProducts()->exists();
    }

    // Get count of e-commerce products
    public function getEcommerceProductsCountAttribute()
    {
        return $this->ecommerceProducts()->count();
    }
}
