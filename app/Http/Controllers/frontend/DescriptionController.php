<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DescriptionController extends Controller
{
     public function description(){
        return view('frontend.description.index');
    }
}
