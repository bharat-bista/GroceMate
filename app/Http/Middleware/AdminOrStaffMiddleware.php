<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrStaffMiddleware
{
    /**
     * Allow admin and staff users into inventory/ecommerce admin panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || !$user->canAccessInventoryPanel()) {
            return redirect()->route('home')
                ->with('popup_error', 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
