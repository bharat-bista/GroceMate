<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\POS\Customer;
use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    /**
     * Set this to TRUE before creating/updating/deleting an Income record
     * from SupplierPaymentController so the model event does NOT
     * auto-adjust the business balance (the controller does it manually).
     * Always reset to FALSE immediately after.
     */
    public static bool $skipBalanceUpdate = false;

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
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount_received'  => 'decimal:2',
    ];

    protected static function booted()
    {
        // ── CREATED ──────────────────────────────────────────────────────────
        static::created(function ($income) {
            // Skip when called from SupplierPaymentController
            // (controller updates business balance manually to avoid double deduction)
            if (static::$skipBalanceUpdate) {
                return;
            }

            if ($income->business_id) {
                $business = Business::find($income->business_id);
                if ($business) {
                    $business->increment('balance', $income->amount_received);
                }
            }
        });

        // ── UPDATED ──────────────────────────────────────────────────────────
        static::updated(function ($income) {
            if (static::$skipBalanceUpdate) {
                return;
            }

            if ($income->wasChanged(['business_id', 'amount_received'])) {

                // Business changed → reverse old, apply new
                if ($income->wasChanged('business_id')) {
                    $oldBusinessId = $income->getOriginal('business_id');
                    $newBusinessId = $income->business_id;

                    if ($oldBusinessId) {
                        $oldBusiness = Business::find($oldBusinessId);
                        if ($oldBusiness) {
                            $oldBusiness->decrement('balance', $income->getOriginal('amount_received'));
                        }
                    }

                    if ($newBusinessId) {
                        $newBusiness = Business::find($newBusinessId);
                        if ($newBusiness) {
                            $newBusiness->increment('balance', $income->amount_received);
                        }
                    }

                // Only amount changed → apply the difference
                } elseif ($income->wasChanged('amount_received') && $income->business_id) {
                    $business = Business::find($income->business_id);
                    if ($business) {
                        $difference = $income->amount_received - $income->getOriginal('amount_received');
                        $business->increment('balance', $difference);
                    }
                }
            }
        });

        // ── DELETED ──────────────────────────────────────────────────────────
        static::deleted(function ($income) {
            if (static::$skipBalanceUpdate) {
                return;
            }

            if ($income->business_id) {
                $business = Business::find($income->business_id);
                if ($business) {
                    // decrement by a negative number = addition, which correctly
                    // reverses a negative amount_received (supplier payment expense)
                    $business->decrement('balance', $income->amount_received);
                }
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getFormattedAmountAttribute(): string
    {
        return 'Rs ' . number_format($this->amount_received, 2);
    }

    public function getAutoReferenceAttribute(): string
    {
        return 'INC-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}