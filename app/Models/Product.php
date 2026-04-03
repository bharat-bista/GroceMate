<?php
// app/Models/Product.php
namespace App\Models;
use App\Models\PurchaseItem;
use App\Models\Brand;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
  protected $fillable = [
    'category_id',
    'brand_id',
    'name',
    'unit',
    'selling_price',
    'is_active',
    'is_listed'
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'is_listed' => 'boolean',
    'selling_price' => 'decimal:2',
  ];

  public function latestPurchaseItem()
  {
      return $this->hasOne(PurchaseItem::class)->latestOfMany();
  }

  public function category() 
  { 
      return $this->belongsTo(Category::class); 
  }

  public function brandRelation() 
  { 
      return $this->belongsTo(Brand::class, 'brand_id'); 
  }

  // Add an accessor for brand name
  public function getBrandNameAttribute()
  {
      return $this->brandRelation ? $this->brandRelation->name : null;
  }

  public function stock() { 
      return $this->hasOne(Stock::class); 
  }

  public function ecommerceProduct()
  {
      return $this->hasOne(EcommerceProduct::class);
  }
}
