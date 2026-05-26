@extends('inventory.layouts.inventory')

@section('title','Business Profile')
@section('heading','Create Business Profile')
@section('subtitle','Manage multiple business accounts professionally')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">

        <!-- Header Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
            <h2 class="text-2xl font-bold">New Business Setup</h2>
            <p class="text-sm opacity-90">Add business details, tax info and branding</p>
        </div>

        <!-- Form Section -->
        <form action="{{ route('business.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf

            <!-- Profile + Owner Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                
                <!-- Profile Image Upload -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-32 h-32 rounded-full border-4 border-indigo-200 overflow-hidden shadow-md">
                        <img id="preview" src="" alt="" class="w-full h-full object-cover hidden" />
                        <div id="preview-placeholder" class="w-full h-full flex flex-col items-center justify-center bg-slate-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l2-3h8l2 3h2a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z"/>
                                <circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="text-xs text-slate-400 mt-1">No image</span>
                        </div>
                    </div>
                    <input type="file" name="profile_image" accept="image/*"
                           class="text-sm file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100"
                           onchange="previewBizImage(this)">
                </div>

                <!-- Owner Info -->
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-600">Owner Name</label>
                        <input type="text" name="owner_name" 
                               class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600">Phone</label>
                        <input type="text" name="phone" 
                               class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">
                    </div>
                </div>
            </div>

            <!-- Business Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-600">Business Name *</label>
                    <input type="text" name="business_name" required
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600">Business Type</label>
                    <select name="business_type"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">
                        <option value="">Select Type</option>
                        <option value="grocery">Grocery</option>
                        <option value="liquor">Liquor</option>
                        
                    </select>
                </div>
            </div>

            <!-- Tax Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-600">PAN Number</label>
                    <input type="text" name="pan_no"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600">VAT Number</label>
                    <input type="text" name="vat_no"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">
                </div>
            </div>

            <!-- Address Section -->
            <div>
                <label class="block text-sm font-medium text-slate-600">Business Address</label>
                <textarea name="address" rows="3"
                          class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-between">
                <a href="{{ route('business.index') }}"
                   data-back-button
                   class="px-6 py-3 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition duration-200">
                    Create Business
                </button>
            </div>

        </form>
    </div>
</div>
@push('scripts')
<script>
function previewBizImage(input) {
    if (input.files && input.files[0]) {
        var img = document.getElementById('preview');
        var placeholder = document.getElementById('preview-placeholder');
        img.src = URL.createObjectURL(input.files[0]);
        img.classList.remove('hidden');
        placeholder.classList.add('hidden');
    }
}
</script>
@endpush

@endsection
