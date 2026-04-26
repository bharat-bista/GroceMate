@extends('inventory.layouts.inventory')

@section('title', 'Access Accounts')
@section('subtitle', 'Manage admin and staff access')
@section('heading', 'Access Accounts')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Admin & Staff Users</h2>
      <p class="text-sm text-slate-500 mt-1">Create users for full admin or restricted staff access.</p>
    </div>
    <a href="{{ route('admin.accounts.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Add Account
    </a>
  </div>

  {{-- Account List --}}
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="text-left px-6 py-3 font-semibold text-slate-600">#</th>
          <th class="text-left px-6 py-3 font-semibold text-slate-600">Full Name</th>
          <th class="text-left px-6 py-3 font-semibold text-slate-600">Email</th>
           <th class="text-left px-6 py-3 font-semibold text-slate-600">Role</th>
           <th class="text-left px-6 py-3 font-semibold text-slate-600">Created</th>
           <th class="text-left px-6 py-3 font-semibold text-slate-600">Status</th>
          <th class="text-right px-6 py-3 font-semibold text-slate-600">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($accounts as $index => $account)
        <tr class="hover:bg-slate-50">
          <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>
          <td class="px-6 py-4 font-medium text-slate-900">
            {{ $account->full_name }}
            @if($account->id === auth()->id())
              <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">You</span>
            @endif
          </td>
          <td class="px-6 py-4 text-slate-600">{{ $account->email }}</td>
          <td class="px-6 py-4">
            @if($account->isStaff())
              <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Staff</span>
            @else
              <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">Admin</span>
            @endif
          </td>
          <td class="px-6 py-4 text-slate-500">{{ $account->created_at?->format('M d, Y') ?? 'N/A' }}</td>
          <td class="px-6 py-4">
            @if($account->status === 'Y')
              <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Active</span>
            @else
              <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Inactive</span>
            @endif
          </td>
          <td class="px-6 py-4 text-right">
            @if($account->id !== auth()->id())
              <form action="{{ route('admin.accounts.destroy', $account) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to remove this account?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                  Remove
                </button>
              </form>
            @else
              <span class="text-xs text-slate-400">—</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-6 py-12 text-center text-slate-400">
            No admin or staff accounts found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Info --}}
  <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
    <strong>Note:</strong> Admin users can access Inventory, POS, and settings.
    Staff users can access Inventory and Ecommerce only. Customers cannot access admin sections.
  </div>

</div>
@endsection
