<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(){
        // Only show brands that have e-commerce products
        $brands = Brand::whereHas('ecommerceProducts')
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        return view('frontend.home.index', compact('brands'));
    }
}
