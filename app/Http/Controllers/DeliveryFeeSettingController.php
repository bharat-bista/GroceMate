<?php

namespace App\Http\Controllers;

use App\Models\DeliveryFeeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DeliveryFeeSettingController extends Controller
{
    public function index()
    {
        $settings = DeliveryFeeSetting::current();

        return view('settings.delivery-fees', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!Schema::hasTable('delivery_fee_settings')) {
            return redirect()->route('delivery-fees.index')
                ->with('error', 'Delivery fee table is missing. Please run: php artisan migrate');
        }

        $validated = $request->validate([
            'inside_fee' => 'required|numeric|min:0|max:999999.99',
            'outside_fee' => 'required|numeric|min:0|max:999999.99',
        ]);

        $settings = DeliveryFeeSetting::current();
        $settings->update($validated);

        return redirect()->route('delivery-fees.index')
            ->with('success', 'Delivery fee settings updated successfully.');
    }
}
