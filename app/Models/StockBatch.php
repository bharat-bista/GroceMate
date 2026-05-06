<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockBatch extends Model
{
    protected $fillable = [
        'product_id',
        'purchase_item_id',
        'batch_no',
        'qty_received',
        'qty_remaining',
        'unit_cost',
        'expiry_date',
        'purchased_on',
        'status',
    ];

    protected $casts = [
        'qty_received' => 'decimal:3',
        'qty_remaining' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'expiry_date' => 'date',
        'purchased_on' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('qty_remaining', '>', 0);
    }

    public function scopeFifo(Builder $query): Builder
    {
        return $query->orderBy('purchased_on')->orderBy('id');
    }

    public static function generateBatchNo(string $date): string
    {
        $purchaseDate = Carbon::parse($date)->toDateString();
        $dateStamp = Carbon::parse($purchaseDate)->format('Ymd');
        $sequence = DB::transaction(function () use ($purchaseDate) {
            return static::whereDate('purchased_on', $purchaseDate)
                ->lockForUpdate()
                ->count() + 1;
        });

        return sprintf('GRO-%s-%04d', $dateStamp, $sequence);
    }
}
