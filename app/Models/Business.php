<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'profile_image'
    ];
}
