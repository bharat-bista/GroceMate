<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\POS\Customer;
use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'customer_id',
        'business_id',
        'created_by',
        'transaction_date',
        'amount_received',
        'payment_method',
        'income_type',
        'description',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount_received' => 'decimal:2',
    ];

    protected static function booted()
    {
        // When income is created
        static::created(function ($income) {
            if ($income->business_id) {
                $business = Business::find($income->business_id);
                if ($business) {
                    $business->increment('balance', $income->amount_received);
                }
            }
        });

        // When income is updated
        static::updated(function ($income) {
            if ($income->wasChanged(['business_id', 'amount_received'])) {
                // Handle business change
                if ($income->wasChanged('business_id')) {
                    $oldBusinessId = $income->getOriginal('business_id');
                    $newBusinessId = $income->business_id;

                    // Remove from old business
                    if ($oldBusinessId) {
                        $oldBusiness = Business::find($oldBusinessId);
                        if ($oldBusiness) {
                            $oldBusiness->decrement('balance', $income->getOriginal('amount_received'));
                        }
                    }

                    // Add to new business
                    if ($newBusinessId) {
                        $newBusiness = Business::find($newBusinessId);
                        if ($newBusiness) {
                            $newBusiness->increment('balance', $income->amount_received);
                        }
                    }
                } 
                // Handle amount change
                elseif ($income->wasChanged('amount_received') && $income->business_id) {
                    $business = Business::find($income->business_id);
                    if ($business) {
                        $difference = $income->amount_received - $income->getOriginal('amount_received');
                        $business->increment('balance', $difference);
                    }
                }
            }
        });

        // When income is deleted
        static::deleted(function ($income) {
            if ($income->business_id) {
                $business = Business::find($income->business_id);
                if ($business) {
                    $business->decrement('balance', $income->amount_received);
                }
            }
        });
    }

    /**
     * Get the customer that owns the income.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the business that owns the income.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user who created the income.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rs ' . number_format($this->amount_received, 2);
    }

    /**
     * Get auto-generated reference number.
     */
    public function getAutoReferenceAttribute(): string
    {
        return 'INC-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
