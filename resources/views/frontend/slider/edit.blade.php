@extends('inventory.layouts.inventory')

@section('title', 'Edit Slider Image')
@section('heading', 'Edit Slider Image')
@section('subtitle', 'Update standalone home hero slide content')

@section('content')
<div class="space-y-6 max-w-4xl">
	<form method="POST" action="{{ route('inventory.sliders.update', $slider) }}" enctype="multipart/form-data"
		  class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">
		@csrf
		@method('PUT')

		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Title *</label>
				<input name="title" value="{{ old('title', $slider->title) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Badge</label>
				<input name="badge" value="{{ old('badge', $slider->badge) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div class="md:col-span-2">
				<label class="block text-sm font-medium text-slate-700 mb-2">Subtitle</label>
				<textarea name="subtitle" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">{{ old('subtitle', $slider->subtitle) }}</textarea>
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Primary Button Text</label>
				<input name="primary_button_text" value="{{ old('primary_button_text', $slider->primary_button_text) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Primary Button Link</label>
				<input name="primary_button_link" value="{{ old('primary_button_link', $slider->primary_button_link) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Secondary Button Text</label>
				<input name="secondary_button_text" value="{{ old('secondary_button_text', $slider->secondary_button_text) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Secondary Button Link</label>
				<input name="secondary_button_link" value="{{ old('secondary_button_link', $slider->secondary_button_link) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Sort Order</label>
				<input name="sort_order" type="number" min="0" value="{{ old('sort_order', $slider->sort_order) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div class="flex items-center gap-3 mt-8">
				<input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $slider->is_active))>
				<label for="is_active" class="text-sm text-slate-700">Active slide</label>
			</div>
			<div class="md:col-span-2">
				<label class="block text-sm font-medium text-slate-700 mb-2">Replace Image</label>
				<input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			@if($slider->image)
				<div class="md:col-span-2">
					<img src="{{ Storage::url($slider->image) }}" alt="Current slide" class="w-64 rounded-xl border border-slate-200">
				</div>
			@endif
		</div>

		<div class="flex items-center gap-3 pt-2">
			<a href="{{ route('inventory.sliders.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">Back</a>
			<button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Update</button>
		</div>
	</form>
</div>
@endsection
