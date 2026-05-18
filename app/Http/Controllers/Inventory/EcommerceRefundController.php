<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\OrderRefund;
use App\Services\EcommerceIncomeSyncService;
use Illuminate\Http\Request;

class EcommerceRefundController extends Controller
{
    public function __construct(private EcommerceIncomeSyncService $incomeSyncService)
    {
    }

    public function update(Request $request, OrderRefund $refund)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return back()->with('error', 'Only admins can mark refunds as completed.');
        }

        if ($refund->refund_status === 'completed') {
            return back()->with('success', 'Refund is already marked as completed.');
        }

        $refund->update([
            'refund_status' => 'completed',
            'refunded_at' => now(),
        ]);

        // Ensure the corresponding order's income entry is removed from the business account.
        // syncOrder() is idempotent — safe to call even if income was already removed at cancellation.
        if ($refund->order) {
            $this->incomeSyncService->syncOrder($refund->order);
        }

        return back()->with('success', 'Refund marked as completed.');
    }
}
