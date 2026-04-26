<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'gender',
        'email',
        'google_id',
        'password',
        'role_id',
        'status',
        'first_time_login',
        'phone_number',
        'image',
        'address'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isCustomer(): bool
    {
        return $this->role_id === 2;
    }

    public function isStaff(): bool
    {
        return $this->role_id === 3;
    }

    public function canAccessInventoryPanel(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }
}
