<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name','phone','email','address'];

    // Later (when you add purchases):
    // public function purchases() { return $this->hasMany(Purchase::class); }
}
