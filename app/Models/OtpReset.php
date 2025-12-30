<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpReset extends Model
{
    protected $table = 'otp_resets';

    protected $fillable = [
        'user_id',
        'otp_hash',
        'expires_at',
        'attempts',
        'used',
        'purpose',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];
}
