@extends('inventory.layouts.inventory')

@section('title', 'Slider Image Details')
@section('heading', 'Slider Image Details')
@section('subtitle', 'Preview standalone home hero slide')

@section('content')
<div class="space-y-6 max-w-4xl">
	<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
		<div class="flex flex-col md:flex-row items-start gap-6">
			<div class="w-72 rounded-xl overflow-hidden border border-slate-200 bg-slate-100">
				@if($slider->image)
					<img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
				@else
					<div class="w-full h-48 flex items-center justify-center text-slate-500">No image</div>
				@endif
			</div>

			<div class="flex-1 space-y-2">
				<h2 class="text-2xl font-bold text-slate-900">{{ $slider->title }}</h2>
				<p class="text-sm text-slate-600">Badge: {{ $slider->badge ?: '-' }}</p>
				<p class="text-sm text-slate-600">Subtitle: {{ $slider->subtitle ?: '-' }}</p>
				<p class="text-sm text-slate-600">Primary button: {{ $slider->primary_button_text ?: '-' }} ({{ $slider->primary_button_link ?: '#' }})</p>
				<p class="text-sm text-slate-600">Secondary button: {{ $slider->secondary_button_text ?: '-' }} ({{ $slider->secondary_button_link ?: '#' }})</p>
				<p class="text-sm text-slate-600">Sort order: {{ $slider->sort_order }}</p>
				<p class="text-sm text-slate-600">Status: {{ $slider->is_active ? 'Active' : 'Inactive' }}</p>

				<div class="flex items-center gap-3 pt-4">
					<a href="{{ route('inventory.sliders.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">Back</a>
					<a href="{{ route('inventory.sliders.edit', $slider) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
