<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnforceOutletRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is authenticated but not a Super Admin, enforce their specific outlet_id
        if ($user && $user->role !== 'Super Admin' && $user->outlet_id) {
            // Override the outlet_id in the request query parameters
            $request->merge(['outlet_id' => $user->outlet_id]);
        }

        return $next($request);
    }
}
