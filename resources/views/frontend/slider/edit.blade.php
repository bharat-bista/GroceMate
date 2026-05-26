@extends('inventory.layouts.inventory')

@section('title','Edit Banner')
@section('heading','Edit Banner')
@section('subtitle','Update hero or promo banner content')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
	<form method="POST" action="{{ route('inventory.sliders.update', $slider) }}" enctype="multipart/form-data"
		  class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
		@csrf
		@method('PUT')

		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Banner Type *</label>
				<select name="slider_type" id="slider_type" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" required>
					<option value="hero" @selected(old('slider_type', $slider->slider_type ?? 'hero') === 'hero')>Hero Slider</option>
					<option value="promo" @selected(old('slider_type', $slider->slider_type) === 'promo')>Promo Banner</option>
				</select>
				@error('slider_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
			</div>
			<div id="promo_slot_wrap" class="{{ old('slider_type', $slider->slider_type) === 'promo' ? '' : 'hidden' }}">
				<label class="block text-sm font-medium text-slate-700 mb-2">Promo Slot (1-4) *</label>
				<select name="promo_slot" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
					<option value="">Select Slot</option>
					@for($slot = 1; $slot <= 4; $slot++)
						<option value="{{ $slot }}" @selected((int) old('promo_slot', $slider->promo_slot) === $slot)>Slot {{ $slot }}</option>
					@endfor
				</select>
				@error('promo_slot')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
			</div>

			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Title *</label>
				<input name="title" value="{{ old('title', $slider->title) }}" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Badge</label>
				<input name="badge" value="{{ old('badge', $slider->badge) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
			</div>
			<div class="md:col-span-2">
				<label class="block text-sm font-medium text-slate-700 mb-2">Subtitle</label>
				<textarea name="subtitle" rows="3" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">{{ old('subtitle', $slider->subtitle) }}</textarea>
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Primary Button Text</label>
				<input name="primary_button_text" value="{{ old('primary_button_text', $slider->primary_button_text) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Primary Button — Link to Category</label>
				@php
					$currentLink = old('primary_button_link', $slider->primary_button_link ?? '');
					$currentCatId = null;
					if ($currentLink && preg_match('/categories(?:\[\]|\[0\])?=(\d+)/', $currentLink, $m)) {
						$currentCatId = (int) $m[1];
					}
				@endphp
				<select name="primary_button_link" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
					<option value="">— No category link —</option>
					@foreach($categories as $cat)
						@php $url = url('/advanced') . '?categories[]=' . $cat->id; @endphp
						<option value="{{ $url }}" @selected($currentCatId === $cat->id)>{{ $cat->name }}</option>
					@endforeach
				</select>
			</div>
			<div id="sort_order_wrap">
				<label class="block text-sm font-medium text-slate-700 mb-2">Sort Order</label>
				<input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $slider->sort_order) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
			</div>
			<div class="flex items-center gap-3 mt-8">
				<input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $slider->is_active))>
				<label for="is_active" class="text-sm text-slate-700">Active slide</label>
			</div>
			<div class="md:col-span-2">
				<label class="block text-sm font-medium text-slate-700 mb-2">Replace Image</label>
				<input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
			</div>
			@if($slider->image)
				<div class="md:col-span-2">
					<img src="{{ Storage::url($slider->image) }}" alt="Current slide" class="w-64 rounded-xl border border-slate-200">
				</div>
			@endif
		</div>

		<div class="flex justify-between pt-4">
			<a href="{{ route('inventory.sliders.index') }}" data-back-button class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">Back</a>
			<button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">Update Banner</button>
		</div>
	</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const typeSelect = document.getElementById('slider_type');
	const promoWrap = document.getElementById('promo_slot_wrap');
	const sortOrderWrap = document.getElementById('sort_order_wrap');
	const sortOrderInput = document.getElementById('sort_order');

	if (!typeSelect || !promoWrap || !sortOrderWrap || !sortOrderInput) return;

	const togglePromoSlot = () => {
		const isPromo = typeSelect.value === 'promo';
		promoWrap.classList.toggle('hidden', !isPromo);
		sortOrderWrap.classList.toggle('hidden', isPromo);
		sortOrderInput.disabled = isPromo;
	};

	typeSelect.addEventListener('change', togglePromoSlot);
	togglePromoSlot();
});
</script>
@endsection
