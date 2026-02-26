<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPosLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('pos_locked', false)) {
            // Allow access to lock page, login, logout, verify-pin, and admin routes
            if (
                !$request->routeIs('pos.lock') &&
                !$request->routeIs('pos.login') &&
                !$request->routeIs('pos.logout') &&
                !$request->routeIs('pos.verify-pin') &&
                !$request->is('admin/*') &&
                !$request->is('admin')
            ) {
                return redirect()->route('pos.lock');
            }
        }

        return $next($request);
    }
}
