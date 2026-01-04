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
        <th class="text-left px-5 py-3">Category Name</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse($categories as $c)
        <tr>
          <td class="px-5 py-4 font-semibold">{{ $c->name }}</td>
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
          <td class="px-5 py-6 text-slate-500" colspan="2">No categories found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $categories->links() }}
</div>
@endsection
