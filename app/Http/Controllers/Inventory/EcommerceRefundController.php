<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\OrderRefund;
use Illuminate\Http\Request;

class EcommerceRefundController extends Controller
{
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

        return back()->with('success', 'Refund marked as completed.');
    }
}
