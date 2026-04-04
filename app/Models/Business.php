<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\POS\Income;
use App\Models\POS\Expense;
use App\Models\POS\Invoice;

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

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class, 'business_account');
    }
}
