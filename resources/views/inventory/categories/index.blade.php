@extends('inventory.layouts.inventory')

@section('title','Categories')
@section('heading','Categories')
@section('subtitle','Create and manage product categories')

@section('content')
<div class="space-y-6">
  <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-200">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-slate-900">Category Management</h2>
          <p class="text-sm text-slate-600 mt-1">Maintain product categories and display order</p>
        </div>
        <a href="{{ route('inventory.categories.create') }}"
           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
          + Add Category
        </a>
      </div>
    </div>

    <div class="p-6 bg-slate-50 border-b border-slate-200">
      <form class="space-y-4" method="GET" action="{{ route('inventory.categories.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
            <input name="q" value="{{ $q }}" placeholder="Search category by name..."
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
          </div>
          <div class="flex items-end gap-3">
            <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
              Search
            </button>
            <a href="{{ route('inventory.categories.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">
              Clear
            </a>
          </div>
        </div>
      </form>
    </div>

    @if($categories->count() > 0)
      <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
        Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} results
      </div>
    @endif

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-100 border-b border-slate-200">
          <tr>
            <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Image</th>
            <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Category Name</th>
            <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Order</th>
            <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
          @forelse($categories as $c)
            <tr class="hover:bg-slate-50">
              <td class="px-6 py-4">
                @if($c->image)
                  <img src="{{ asset('storage/' . $c->image) }}" alt="{{ $c->name }}" class="h-10 w-10 object-cover rounded-lg border border-slate-200">
                @else
                  <div class="h-10 w-10 bg-slate-100 rounded-lg border border-slate-200"></div>
                @endif
              </td>
              <td class="px-6 py-4 font-medium text-slate-900">{{ $c->name }}</td>
              <td class="px-6 py-4 text-slate-600">{{ $c->order }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex space-x-3">
                  <a class="text-emerald-600 hover:text-emerald-900 font-medium" href="{{ route('inventory.categories.edit',$c) }}">Edit</a>
                  <form method="POST" action="{{ route('inventory.categories.destroy',$c) }}" onsubmit="return confirm('Delete this category?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-6 py-12 text-center text-slate-500" colspan="4">No categories found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($categories->hasPages())
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
        {{ $categories->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
