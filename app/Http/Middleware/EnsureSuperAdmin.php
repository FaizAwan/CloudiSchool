<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $routeName = optional($request->route())->getName();
        if (session()->has('impersonator_id') && $routeName === 'superadmin.leave_impersonation') {
            return $next($request);
        }

        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        return $next($request);
    }
}
