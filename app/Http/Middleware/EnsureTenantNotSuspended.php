<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantNotSuspended
{
    public function handle(Request $request, Closure $next)
    {
        // Only act if tenancy is initialized and suspended flag is set
        try {
            $routeName = optional($request->route())->getName();
            if ($routeName === 'superadmin.leave_impersonation' || (function_exists('session') && session()->has('impersonator_id'))) {
                return $next($request);
            }
            if (function_exists('tenant') && tenant()) {
                $suspended = (bool) (tenant('suspended') ?? false);
                if ($suspended) {
                    return response()->view('tenant.suspended', [], 403);
                }
            }
        } catch (\Throwable $e) {
        }

        return $next($request);
    }
}
