<?php
// app/Models/Stock.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
    'quantity' => 'integer',
    'reorder_level' => 'integer',
  ];

  public function scopeLowStock(Builder $query): Builder
  {
    return $query->where(function (Builder $stockQuery) {
      $stockQuery
        ->where(function (Builder $configuredQuery) {
          $configuredQuery
            ->where('reorder_level', '>', 0)
            ->whereColumn('quantity', '<=', 'reorder_level');
        })
        ->orWhere(function (Builder $fallbackQuery) {
          $fallbackQuery
            ->where('reorder_level', '<=', 0)
            ->where('quantity', '<=', 0);
        });
    });
  }

  public function product() { 
    return $this->belongsTo(Product::class); }
}
