<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'customer_type',
        'opening_due',
        'total_due',
        'address',
        'notes'
    ];
}
