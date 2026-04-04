<?php

namespace App\Models\POS;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    public static bool $skipBalanceUpdate = false;

    protected $fillable = [
        'reference_no',
        'business_id',
        'created_by',
        'transaction_date',
        'amount',
        'payment_method',
        'expense_type',
        'description',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::created(function ($expense) {
            if (static::$skipBalanceUpdate) {
                return;
            }

            if ($expense->business_id) {
                $business = Business::find($expense->business_id);
                if ($business) {
                    $business->decrement('balance', $expense->amount);
                }
            }
        });

        static::updated(function ($expense) {
            if (static::$skipBalanceUpdate) {
                return;
            }

            if ($expense->wasChanged(['business_id', 'amount'])) {
                if ($expense->wasChanged('business_id')) {
                    $oldBusinessId = $expense->getOriginal('business_id');
                    $newBusinessId = $expense->business_id;

                    if ($oldBusinessId) {
                        $oldBusiness = Business::find($oldBusinessId);
                        if ($oldBusiness) {
                            $oldBusiness->increment('balance', $expense->getOriginal('amount'));
                        }
                    }

                    if ($newBusinessId) {
                        $newBusiness = Business::find($newBusinessId);
                        if ($newBusiness) {
                            $newBusiness->decrement('balance', $expense->amount);
                        }
                    }
                } elseif ($expense->wasChanged('amount') && $expense->business_id) {
                    $business = Business::find($expense->business_id);
                    if ($business) {
                        $difference = $expense->amount - $expense->getOriginal('amount');
                        $business->decrement('balance', $difference);
                    }
                }
            }
        });

        static::deleted(function ($expense) {
            if (static::$skipBalanceUpdate) {
                return;
            }

            if ($expense->business_id) {
                $business = Business::find($expense->business_id);
                if ($business) {
                    $business->increment('balance', $expense->amount);
                }
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
