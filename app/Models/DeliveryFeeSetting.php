<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DeliveryFeeSetting extends Model
{
    public const DEFAULT_INSIDE_FEE = 100;
    public const DEFAULT_OUTSIDE_FEE = 200;

    protected $fillable = [
        'inside_fee',
        'outside_fee',
    ];

    protected $casts = [
        'inside_fee' => 'decimal:2',
        'outside_fee' => 'decimal:2',
    ];

    public static function current(): self
    {
        if (!Schema::hasTable('delivery_fee_settings')) {
            return new self([
                'inside_fee' => self::DEFAULT_INSIDE_FEE,
                'outside_fee' => self::DEFAULT_OUTSIDE_FEE,
            ]);
        }

        return static::query()->firstOrCreate([], [
            'inside_fee' => self::DEFAULT_INSIDE_FEE,
            'outside_fee' => self::DEFAULT_OUTSIDE_FEE,
        ]);
    }

    public static function chargeMap(): array
    {
        $settings = static::current();

        return [
            'inside' => (float) $settings->inside_fee,
            'outside' => (float) $settings->outside_fee,
            'pickup' => 0.0,
        ];
    }
}
