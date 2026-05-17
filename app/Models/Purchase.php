<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'business_id','supplier_id','created_by','purchase_date','invoice_no','total_cost'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_cost' => 'integer',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function business() { return $this->belongsTo(Business::class); }
    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }
    public function items()    { return $this->hasMany(PurchaseItem::class); }
}
