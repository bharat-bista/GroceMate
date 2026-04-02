@extends('inventory.layouts.inventory')

@section('title','Categories')
@section('heading','Categories')
@section('subtitle','Create and manage product categories')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <form class="flex gap-2" method="GET" action="{{ route('inventory.categories.index') }}">
    <input name="q" value="{{ $q }}" placeholder="Search category..."
           class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
    <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Search
    </button>
  </form>

  <a href="{{ route('inventory.categories.create') }}"
     class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
    + Add Category
  </a>
</div>

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">Image</th>
        <th class="text-left px-5 py-3">Category Name</th>
        <th class="text-center px-5 py-3">Order</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse($categories as $c)
        <tr>
          <td class="px-5 py-4">
            @if($c->image)
              <img src="{{ asset('storage/' . $c->image) }}" alt="{{ $c->name }}" class="h-10 w-10 object-cover rounded-lg">
            @else
              <div class="h-10 w-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            @endif
          </td>
          <td class="px-5 py-4 font-semibold">{{ $c->name }}</td>
          <td class="px-5 py-4 text-center">{{ $c->order }}</td>
          <td class="px-5 py-4 text-right">
            <div class="inline-flex items-center gap-3">
              <a class="underline text-slate-800" href="{{ route('inventory.categories.edit',$c) }}">Edit</a>

              <form method="POST" action="{{ route('inventory.categories.destroy',$c) }}"
                    onsubmit="return confirm('Delete this category?');">
                @csrf
                @method('DELETE')
                <button class="text-red-700 underline">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td class="px-5 py-6 text-slate-500" colspan="4">No categories found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $categories->links() }}
</div>
@endsection
