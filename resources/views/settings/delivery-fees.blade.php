@extends('inventory.layouts.inventory')

@section('title', 'Delivery Fee Settings')
@section('subtitle', 'Manage ecommerce delivery charges')
@section('heading', 'Delivery Fee Settings')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="bg-white rounded-xl border border-slate-200 p-6">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900">Delivery Fee Settings</h1>
      <p class="text-slate-500 mt-1">Update default delivery charges used in ecommerce checkout.</p>
    </div>

    <form method="POST" action="{{ route('delivery-fees.update') }}" class="space-y-6">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="inside_fee" class="block text-sm font-medium text-slate-700 mb-1">Inside Valley Fee (Rs.)</label>
          <input
            id="inside_fee"
            name="inside_fee"
            type="number"
            min="0"
            step="1"
            data-money inputmode="numeric" max="9999999"
            value="{{ old('inside_fee', $settings->inside_fee) }}"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
            required
          >
          @error('inside_fee')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="outside_fee" class="block text-sm font-medium text-slate-700 mb-1">Outside Valley Fee (Rs.)</label>
          <input
            id="outside_fee"
            name="outside_fee"
            type="number"
            min="0"
            step="1"
            data-money inputmode="numeric" max="9999999"
            value="{{ old('outside_fee', $settings->outside_fee) }}"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
            required
          >
          @error('outside_fee')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600">
        Store pickup remains free (Rs. 0).
      </div>

      <div class="pt-2">
        <button
          type="submit"
          class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition"
        >
          Save Delivery Fees
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
