<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'vat_number',
        'pan_number',
        'supplier_type',
        'opening_due',
        'total_due',
        'address'
    ];

    // Later (when you add purchases):
    // public function purchases() { return $this->hasMany(Purchase::class); }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function getCalculatedTotalDueAttribute()
    {
        return $this->total_due;
    }
}
