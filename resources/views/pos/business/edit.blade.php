@extends('inventory.layouts.inventory')

@section('title','Edit Business Profile')
@section('heading','Edit Business Profile')
@section('subtitle','Update business details and information')

@section('content')
<div class="max-w-5xl mx-auto">

    @if(session('success'))
        <div id="success-message" class="mb-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
            <h2 class="text-2xl font-bold">Edit Business: {{ $business->business_name }}</h2>
            <p class="text-sm opacity-90">Update business details, tax info and branding</p>
        </div>

        <!-- Form Section -->
        <form action="{{ route('business.update', $business) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <!-- Profile + Owner Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                
                <!-- Profile Image Upload -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-32 h-32 rounded-full border-4 border-indigo-200 overflow-hidden shadow-md">
                        @if($business->profile_image)
                            <img id="preview" src="/assets/img/business/{{ $business->profile_image }}" 
                                 class="w-full h-full object-cover" alt="{{ $business->business_name }}" />
                        @else
                            <div id="preview" class="w-full h-full flex items-center justify-center bg-slate-100">
                                <i class="fas fa-camera text-3xl text-slate-400"></i>
                            </div>
                        @endif
                    </div>
                    <input type="file" name="profile_image" accept="image/*" 
                           class="text-sm file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100"
                           onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])">
                </div>

                <!-- Owner Info -->
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-600">Owner Name</label>
                        <input type="text" name="owner_name" value="{{ $business->owner_name ?? '' }}"
                               class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600">Phone</label>
                        <input type="text" name="phone" value="{{ $business->phone ?? '' }}"
                               class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Business Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-600">Business Name *</label>
                    <input type="text" name="business_name" value="{{ $business->business_name }}" required
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600">Business Type</label>
                    <select name="business_type"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">Select Type</option>
                        <option value="grocery" {{ $business->business_type == 'grocery' ? 'selected' : '' }}>Grocery</option>
                        <option value="liquor" {{ $business->business_type == 'liquor' ? 'selected' : '' }}>Liquor</option>
                        <option value="wholesale" {{ $business->business_type == 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                        <option value="retail" {{ $business->business_type == 'retail' ? 'selected' : '' }}>Retail</option>
                    </select>
                </div>
            </div>

            <!-- Tax Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-600">PAN Number</label>
                    <input type="text" name="pan_no" value="{{ $business->pan_no ?? '' }}"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600">VAT Number</label>
                    <input type="text" name="vat_no" value="{{ $business->vat_no ?? '' }}"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
            </div>

            <!-- Address Section -->
            <div>
                <label class="block text-sm font-medium text-slate-600">Business Address</label>
                <textarea name="address" rows="3"
                          class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ $business->address ?? '' }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between">
                <a href="{{ route('business.index') }}" 
                   class="px-6 py-3 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Businesses
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Update Business
                </button>
            </div>

        </form>
    </div>
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