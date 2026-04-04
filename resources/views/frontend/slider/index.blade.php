@extends('inventory.layouts.inventory')

@section('title', 'Slider Image')
@section('subtitle', 'Manage standalone slides for the home hero section')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Home Hero Slides</h2>
            <p class="text-sm text-slate-500">These slides are shown on the homepage hero carousel.</p>
        </div>
        <a href="{{ route('inventory.sliders.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
            <i class="fas fa-plus"></i>
            Add Slide
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Image</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Badge</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Order</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sliders as $slide)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="h-12 w-20 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                                @if($slide->image)
                                    <img src="{{ Storage::url($slide->image) }}" alt="{{ $slide->title }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $slide->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $slide->badge ?: '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $slide->sort_order }}</td>
                        <td class="px-4 py-3">
                            @if($slide->is_active)
                                <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Active</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('inventory.sliders.show', $slide) }}" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200">View</a>
                                <a href="{{ route('inventory.sliders.edit', $slide) }}" class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200">Edit</a>
                                <form method="POST" action="{{ route('inventory.sliders.destroy', $slide) }}" onsubmit="return confirm('Delete this slide?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">No slides available. Click "Add Slide" to create one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $sliders->links() }}
    </div>
</div>
@endsection
