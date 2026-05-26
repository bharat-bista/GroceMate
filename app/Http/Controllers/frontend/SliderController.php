<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::query()
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $promoCount = Slider::query()
            ->where('slider_type', 'promo')
            ->count();

        $totalSliders = Slider::query()->count();
        $activeSliders = Slider::query()->where('is_active', true)->count();
        $heroCount = Slider::query()
            ->where(function ($query) {
                $query->where('slider_type', 'hero')
                    ->orWhereNull('slider_type');
            })
            ->count();

        return view('frontend.slider.index', compact('sliders', 'promoCount', 'totalSliders', 'activeSliders', 'heroCount'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('frontend.slider.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:1000'],
            'badge' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'primary_button_text' => ['nullable', 'string', 'max:100'],
            'primary_button_link' => ['nullable', 'string', 'max:255'],
            'slider_type' => ['required', 'in:hero,promo'],
            'promo_slot' => ['nullable', 'integer', 'between:1,4', 'required_if:slider_type,promo'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (($data['slider_type'] ?? 'hero') === 'promo') {
            $promoCount = Slider::query()->where('slider_type', 'promo')->count();
            if ($promoCount >= 4) {
                return back()
                    ->withErrors(['slider_type' => 'Only 4 promo banners are allowed.'])
                    ->withInput();
            }

            $slotTaken = Slider::query()
                ->where('slider_type', 'promo')
                ->where('promo_slot', $data['promo_slot'])
                ->exists();

            if ($slotTaken) {
                return back()
                    ->withErrors(['promo_slot' => 'This promo slot is already in use. Choose another slot (1-4).'])
                    ->withInput();
            }

            // Promo banners use promo slot as their effective order.
            $data['sort_order'] = (int) $data['promo_slot'];
        } else {
            $data['promo_slot'] = null;
            $data['sort_order'] = $data['sort_order'] ?? 0;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        Slider::create($data);
        $this->clearSliderCache();

        return redirect()
            ->route('inventory.sliders.index')
            ->with('success', 'Slider created successfully.');
    }

    public function show(Slider $slider)
    {
        return view('frontend.slider.show', compact('slider'));
    }

    public function edit(Slider $slider)
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('frontend.slider.edit', compact('slider', 'categories'));
    }

    public function update(Request $request, Slider $slider)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:1000'],
            'badge' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'primary_button_text' => ['nullable', 'string', 'max:100'],
            'primary_button_link' => ['nullable', 'string', 'max:255'],
            'slider_type' => ['required', 'in:hero,promo'],
            'promo_slot' => ['nullable', 'integer', 'between:1,4', 'required_if:slider_type,promo'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (($data['slider_type'] ?? 'hero') === 'promo') {
            $promoCount = Slider::query()
                ->where('slider_type', 'promo')
                ->where('id', '!=', $slider->id)
                ->count();

            if ($promoCount >= 4) {
                return back()
                    ->withErrors(['slider_type' => 'Only 4 promo banners are allowed.'])
                    ->withInput();
            }

            $slotTaken = Slider::query()
                ->where('slider_type', 'promo')
                ->where('promo_slot', $data['promo_slot'])
                ->where('id', '!=', $slider->id)
                ->exists();

            if ($slotTaken) {
                return back()
                    ->withErrors(['promo_slot' => 'This promo slot is already in use. Choose another slot (1-4).'])
                    ->withInput();
            }

            // Promo banners use promo slot as their effective order.
            $data['sort_order'] = (int) $data['promo_slot'];
        } else {
            $data['promo_slot'] = null;
            $data['sort_order'] = $data['sort_order'] ?? 0;
        }

        if ($request->hasFile('image')) {
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $slider->update($data);
        $this->clearSliderCache();

        return redirect()
            ->route('inventory.sliders.index')
            ->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();
        $this->clearSliderCache();

        return redirect()
            ->route('inventory.sliders.index')
            ->with('success', 'Slider deleted successfully.');
    }

    private function clearSliderCache(): void
    {
        Cache::forget('storefront.home.hero_slides');
        Cache::forget('storefront.home.promo_slides');
    }
}
