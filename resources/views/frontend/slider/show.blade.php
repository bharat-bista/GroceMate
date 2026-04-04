@extends('inventory.layouts.inventory')

@section('title','Banner Details')
@section('heading','Banner Details')
@section('subtitle','Preview hero or promo banner details')

@section('content')
<div class="space-y-6">
	@if(session('success'))
		<div class="p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
			{{ session('success') }}
		</div>
	@endif

	<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
		<div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
			<div>
				<h2 class="text-2xl font-bold text-slate-900">{{ $slider->title }}</h2>
				<p class="text-sm text-slate-600 mt-1">Banner configuration and display details</p>
			</div>
			<div>
				@if($slider->is_active)
					<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Active</span>
				@else
					<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Inactive</span>
				@endif
			</div>
		</div>

		<div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
			<div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 min-h-[220px]">
				@if($slider->image)
					<img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
				@else
					<div class="w-full h-full min-h-[220px] flex items-center justify-center text-slate-500">No image uploaded</div>
				@endif
			</div>

			<div class="space-y-4">
				<div>
					<p class="text-xs text-slate-500 uppercase tracking-wide">Type</p>
					<p class="text-base font-semibold text-slate-900 mt-1">{{ ucfirst($slider->slider_type ?? 'hero') }}</p>
				</div>
				@if(($slider->slider_type ?? 'hero') === 'promo')
					<div>
						<p class="text-xs text-slate-500 uppercase tracking-wide">Promo Slot</p>
						<p class="text-base font-semibold text-slate-900 mt-1">{{ $slider->promo_slot ?: '-' }}</p>
					</div>
				@endif
				<div>
					<p class="text-xs text-slate-500 uppercase tracking-wide">Badge</p>
					<p class="text-base font-semibold text-slate-900 mt-1">{{ $slider->badge ?: '-' }}</p>
				</div>
				<div>
					<p class="text-xs text-slate-500 uppercase tracking-wide">Subtitle</p>
					<p class="text-sm text-slate-700 mt-1">{{ $slider->subtitle ?: '-' }}</p>
				</div>
				<div>
					<p class="text-xs text-slate-500 uppercase tracking-wide">Primary Button</p>
					<p class="text-sm text-slate-700 mt-1">{{ $slider->primary_button_text ?: '-' }} ({{ $slider->primary_button_link ?: '#' }})</p>
				</div>
				<div>
					<p class="text-xs text-slate-500 uppercase tracking-wide">Secondary Button</p>
					<p class="text-sm text-slate-700 mt-1">{{ $slider->secondary_button_text ?: '-' }} ({{ $slider->secondary_button_link ?: '#' }})</p>
				</div>
				<div>
					<p class="text-xs text-slate-500 uppercase tracking-wide">Sort Order</p>
					<p class="text-base font-semibold text-slate-900 mt-1">{{ $slider->sort_order }}</p>
				</div>
			</div>
		</div>

		<div class="px-6 pb-6 flex flex-wrap gap-3">
			<a href="{{ route('inventory.sliders.index') }}" data-back-button class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">Back</a>
			<a href="{{ route('inventory.sliders.edit', $slider) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Edit Banner</a>
		</div>
	</div>
</div>
@endsection
