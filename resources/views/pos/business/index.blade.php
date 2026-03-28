@extends('inventory.layouts.inventory')

@section('title','Business Profiles')
@section('heading','Business Profiles')
@section('subtitle','Manage all business accounts')

@section('content')
<div class="max-w-7xl mx-auto">

    @if(session('success'))
        <div id="success-message" class="mb-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">All Businesses</h2>
            <p class="text-slate-600">Manage your business profiles and settings</p>
        </div>
        <a href="{{ route('business.create') }}" 
           class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition duration-200"
>
            <i class="fas fa-plus mr-2"></i>Add Business
        </a>
    </div>

    <!-- Business Cards Grid -->
    @if($businesses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($businesses as $business)
                <div class="bg-white rounded-md border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden cursor-pointer"
                     onclick="window.location='{{ route('business.show', $business) }}'">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-3 text-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-16 h-16 rounded-lg bg-white/20 overflow-hidden border-2 border-white/30">
                                @if($business->profile_image)
                                    <img src="/assets/img/business/{{ $business->profile_image }}" 
                                         class="w-full h-full object-cover" alt="{{ $business->business_name }}" 
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\'><i class=\'fas fa-building text-2xl text-white\'></i></div>'" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-building text-xl text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-lg truncate">{{ $business->business_name }}</h3>
                                <p class="text-sm opacity-90 truncate">{{ $business->business_type ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-2">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-user w-4 text-slate-400 text-xs"></i>
                            <span class="ml-2 text-slate-700 truncate">{{ $business->owner_name ?? 'Not specified' }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-phone w-4 text-slate-400 text-xs"></i>
                            <span class="ml-2 text-slate-700 truncate">{{ $business->phone ?? 'Not specified' }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-file-invoice w-4 text-slate-400 text-xs"></i>
                            <span class="ml-2 text-slate-700 truncate">PAN: {{ $business->pan_no ?? 'Not specified' }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-receipt w-4 text-slate-400 text-xs"></i>
                            <span class="ml-2 text-slate-700 truncate">VAT: {{ $business->vat_no ?? 'Not specified' }}</span>
                        </div>

                        <div class="text-sm">
                            <i class="fas fa-map-marker-alt w-4 text-slate-400 text-xs"></i>
                            <span class="ml-2 text-slate-700">Address: {{ Str::limit($business->address ?? 'Not specified', 30) }}</span>
                        </div>
                    </div>

                    <!-- Card Footer -->
<div class="bg-slate-100 px-4 py-3 flex justify-end gap-2 border-t border-slate-200">
    <a href="{{ route('business.show', $business) }}"
       onclick="event.stopPropagation();"
       class="px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-md hover:bg-emerald-500 transition duration-200 flex items-center gap-1">
        <i class="fas fa-eye"></i> View
    </a>
    <a href="{{ route('business.edit', $business) }}" 
       onclick="event.stopPropagation();"
       class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-300 transition duration-200 flex items-center gap-1">
        <i class="fas fa-edit"></i> Edit
    </a>
</div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <i class="fas fa-building text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2">No Businesses Yet</h3>
            <p class="text-slate-600 mb-6">Get started by creating your first business profile</p>
            <a href="{{ route('business.create') }}" 
               class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition duration-200">
                <i class="fas fa-plus mr-2"></i>Create Business
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.transition = 'opacity 0.5s ease-out';
            successMessage.style.opacity = '0';
            setTimeout(function() {
                successMessage.remove();
            }, 500);
        }, 4000);
    }
});
</script>
@endpush
