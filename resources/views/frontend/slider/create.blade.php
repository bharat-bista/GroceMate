@extends('inventory.layouts.inventory')

@section('title', 'Add Slider Image')
@section('heading', 'Add Slider Image')
@section('subtitle', 'Create a standalone slide for the home page hero section')

@section('content')
<div class="space-y-6">
	<form method="POST" action="{{ route('inventory.sliders.store') }}" enctype="multipart/form-data"
		  class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5 max-w-4xl">
		@csrf

		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Title *</label>
				<input name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
				@error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Badge</label>
				<input name="badge" value="{{ old('badge') }}" placeholder="e.g. EXPRESS DELIVERY" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
				@error('badge')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
			</div>
			<div class="md:col-span-2">
				<label class="block text-sm font-medium text-slate-700 mb-2">Subtitle</label>
				<textarea name="subtitle" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">{{ old('subtitle') }}</textarea>
				@error('subtitle')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Primary Button Text</label>
				<input name="primary_button_text" value="{{ old('primary_button_text', 'Shop Now') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Primary Button Link</label>
				<input name="primary_button_link" value="{{ old('primary_button_link', '#') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Secondary Button Text</label>
				<input name="secondary_button_text" value="{{ old('secondary_button_text', 'View Deals') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Secondary Button Link</label>
				<input name="secondary_button_link" value="{{ old('secondary_button_link', '#') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 mb-2">Sort Order</label>
				<input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
			</div>
			<div class="flex items-center gap-3 mt-8">
				<input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
				<label for="is_active" class="text-sm text-slate-700">Active slide</label>
			</div>
			<div class="md:col-span-2">
				<label class="block text-sm font-medium text-slate-700 mb-2">Slide Image</label>
				<input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
				@error('image')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
			</div>
		</div>

		<div class="flex items-center gap-3 pt-2">
			<a href="{{ route('inventory.sliders.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">Back</a>
			<button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Save Slide</button>
		</div>
	</form>
</div>
@endsection
