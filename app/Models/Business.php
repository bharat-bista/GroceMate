<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\POS\Income;

class Business extends Model
{
    protected $fillable = [
        'business_name',
        'business_type',
        'vat_no',
        'pan_no',
        'phone',
        'address',
        'owner_name',
        'profile_image',
        'balance'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get the incomes for the business.
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
}
