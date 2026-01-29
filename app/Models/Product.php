<?php
// app/Models/Product.php
namespace App\Models;
use App\Models\PurchaseItem;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
  protected $fillable = [
    'category_id',
    'name',
    'sku',
    'unit',
    'selling_price',
    'description',
    'image_url',
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
  { return 
    $this->belongsTo(Category::class); 
}
  public function stock() { 
    return $this->hasOne(Stock::class); 
}

}
