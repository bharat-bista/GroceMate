@extends('inventory.layouts.inventory')

@section('title', 'Create Tax')
@section('subtitle', 'Add a new tax configuration')
@section('heading', 'Create Tax')

@section('content')
<div class="max-w-2xl mx-auto">
  <!-- Back Link -->
  <a href="{{ route('taxes.index') }}"
     class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-6">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Back to Taxes
  </a>

  <!-- Form Card -->
  <div class="bg-white rounded-xl border border-slate-200 p-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-1">Tax Information</h3>
    <p class="text-sm text-slate-500 mb-6">Enter the details for the new tax configuration.</p>

    <form action="{{ route('taxes.store') }}" method="POST" class="space-y-6">
      @csrf

      <!-- Tax Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Tax Name</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required
               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
               placeholder="e.g., VAT, Sales Tax, Excise Duty">
        @error('name')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <!-- Tax Type -->
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tax Type</label>
        <div class="space-y-3">
          <label class="flex items-center">
            <input type="radio" name="type" value="percentage" {{ old('type', 'percentage') === 'percentage' ? 'checked' : '' }}
                   class="w-4 h-4 text-green-600 border-slate-300 focus:ring-green-500">
            <span class="ml-2 text-sm text-slate-700">Percentage (%)</span>
          </label>
          <label class="flex items-center">
            <input type="radio" name="type" value="fixed" {{ old('type') === 'fixed' ? 'checked' : '' }}
                   class="w-4 h-4 text-green-600 border-slate-300 focus:ring-green-500">
            <span class="ml-2 text-sm text-slate-700">Fixed Amount (Rs)</span>
          </label>
        </div>
        @error('type')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <!-- Tax Rate -->
      <div>
        <label for="rate" class="block text-sm font-medium text-slate-700 mb-1">Tax Rate</label>
        <div class="relative">
          <input type="number" id="rate" name="rate" value="{{ old('rate') }}" required
                 step="0.01" min="0" max="100"
                 class="w-full px-4 py-3 pr-16 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                 placeholder="0.00">
          <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-500" id="rate-suffix">%</span>
        </div>
        @error('rate')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <!-- Submit Buttons -->
      <div class="flex items-center gap-3 pt-4">
        <button type="submit"
                class="flex-1 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Create Tax
        </button>
        <a href="{{ route('taxes.index') }}"
           class="px-6 py-3 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const rateInput = document.getElementById('rate');
    const rateSuffix = document.getElementById('rate-suffix');
    
    function updateRateSuffix() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;
        if (selectedType === 'percentage') {
            rateSuffix.textContent = '%';
            rateInput.max = '100';
        } else {
            rateSuffix.textContent = 'Rs';
            rateInput.removeAttribute('max');
        }
    }
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateRateSuffix);
    });
    
    updateRateSuffix();
});
</script>
@endsection
