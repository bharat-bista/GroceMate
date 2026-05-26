@extends('inventory.layouts.inventory')

@section('title','Slider Banners')
@section('heading','Slider Banners')
@section('subtitle','Manage hero and promo banners from one place')

@section('content')
<div class="space-y-6">
	<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
		<div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white border-2 border-black">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-blue-100 text-sm font-medium">Total Banners</p>
					<p class="text-3xl font-bold mt-2">{{ $totalSliders }}</p>
				</div>
				<div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
						<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
					</svg>
				</div>
			</div>
		</div>

		<div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white border-2 border-black">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-emerald-100 text-sm font-medium">Active Banners</p>
					<p class="text-3xl font-bold mt-2">{{ $activeSliders }}</p>
				</div>
				<div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
						<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
					</svg>
				</div>
			</div>
		</div>

		<div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-6 text-white border-2 border-black">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-amber-100 text-sm font-medium">Promo Banners</p>
					<p class="text-3xl font-bold mt-2">{{ $promoCount }}/4</p>
				</div>
				<div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
					</svg>
				</div>
			</div>
		</div>
	</div>

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Banner Management</h2>
                    <p class="text-sm text-slate-600 mt-1">Create and manage hero slides and promo banners.</p>
                </div>
                <a href="{{ route('inventory.sliders.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Banner
                </a>
            </div>
        </div>

        @if($sliders->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $sliders->firstItem() }} to {{ $sliders->lastItem() }} of {{ $sliders->total() }} results
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Image</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Title</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Type</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Badge</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Order</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($sliders as $slide)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="h-12 w-20 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                                    @if($slide->image)
                                        <img src="{{ Storage::url($slide->image) }}" alt="{{ $slide->title }}" class="h-full w-full object-cover">
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $slide->title }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                @if(($slide->slider_type ?? 'hero') === 'promo')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Promo</span>
                                    <span class="text-xs text-slate-500 ml-1">Slot {{ $slide->promo_slot ?? '-' }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Hero</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $slide->badge ?: '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $slide->sort_order }}</td>
                            <td class="px-6 py-4">
                                @if($slide->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-3">
                                    <a href="{{ route('inventory.sliders.show', $slide) }}" class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                                    <a href="{{ route('inventory.sliders.edit', $slide) }}" class="text-emerald-600 hover:text-emerald-900 font-medium">Edit</a>
                                    <form method="POST" action="{{ route('inventory.sliders.destroy', $slide) }}" class="inline" onsubmit="return confirm('Delete this slide?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <p class="text-lg font-medium">No banners found</p>
                                    <p class="text-sm mt-1">Create your first hero or promo banner.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('inventory.sliders.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                                            Add Banner
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sliders->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $sliders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
