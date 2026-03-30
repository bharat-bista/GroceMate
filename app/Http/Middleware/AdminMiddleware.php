<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Only allow admin users (role_id = 1) to proceed.
     * Non-admin users are redirected to the customer home page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('home')
                ->with('popup_error', 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
