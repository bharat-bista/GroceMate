<?php
// app/Models/Stock.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model {
  protected $table = 'stock';
  protected $primaryKey = 'product_id';
  public $incrementing = false;
  protected $fillable = [
    'product_id',
    'quantity',
    'reorder_level'
];
  protected $casts = [
    'quantity' => 'decimal:3',
    'reorder_level' => 'decimal:3',
  ];

  public function product() { 
    return $this->belongsTo(Product::class); }
}
