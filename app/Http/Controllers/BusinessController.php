<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::latest()->get();
        return view('pos.business.index', compact('businesses'));
    }

    public function create()
    {
        return view('pos.business.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'vat_no' => $request->vat_no,
            'pan_no' => $request->pan_no,
            'phone' => $request->phone,
            'address' => $request->address,
            'owner_name' => $request->owner_name,
        ];

        // Handle image upload - store as filename
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/business'), $imageName);
            $data['profile_image'] = $imageName;
        }

        Business::create($data);

        return redirect()->route('business.index')->with('success', 'Business Created Successfully!');
    }

    public function edit(Business $business)
    {
        return view('pos.business.edit', compact('business'));
    }

    public function update(Request $request, Business $business)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'vat_no' => $request->vat_no,
            'pan_no' => $request->pan_no,
            'phone' => $request->phone,
            'address' => $request->address,
            'owner_name' => $request->owner_name,
        ];

        // Handle image upload - store as filename
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/business'), $imageName);
            $data['profile_image'] = $imageName;
        }

        $business->update($data);

        return redirect()->route('business.index')->with('success', 'Business Updated Successfully!');
    }

    public function destroy(Business $business)
    {
        // Delete associated image file if exists
        if ($business->profile_image) {
            $imagePath = public_path('assets/img/business/' . $business->profile_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete the business record
        $business->delete();

        return redirect()->route('business.index')->with('success', 'Business Deleted Successfully!');
    }

    public function getImage(Business $business)
    {
        if (!$business->profile_image) {
            abort(404);
        }

        // Get binary image data
        $imageData = $business->profile_image;
        
        // Ensure it's binary data, not string
        if (is_string($imageData)) {
            $imageData = $imageData;
        }

        // Try to detect MIME type, fallback to jpeg if detection fails
        $mimeType = 'image/jpeg'; // default fallback
        try {
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detectedType = finfo_buffer($finfo, $imageData);
                finfo_close($finfo);
                if ($detectedType && strpos($detectedType, 'image/') === 0) {
                    $mimeType = $detectedType;
                }
            }
        } catch (\Exception $e) {
            // If detection fails, use default
        }

        return response($imageData)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', strlen($imageData))
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}
