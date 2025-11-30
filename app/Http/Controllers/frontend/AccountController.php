<?php

namespace App\Http\Controllers\frontend;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function login(){
        return view('frontend.account.login');
    }

    public function store(Request $request)
    {
        // Validate email and password
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log the user in using the 'web' guard
        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            // Redirect to intended route (dashboard) with success message
            return redirect()->intended(route('home'))->with('success', 'Login successful!');
        }

        // If login fails, redirect back with error message
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }
        // Show dashboard page
    public function HomePage()
    {
        return view('home');
    }

}
