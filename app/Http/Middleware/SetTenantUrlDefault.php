<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class SetTenantUrlDefault
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('local')) {
            $tenantId = $request->route('tenant') ?: $request->route('tenant_id');

            if (!$tenantId && function_exists('tenant')) {
                try {
                    $tenantId = tenant('id');
                } catch (\Throwable $e) {
                }
            }

            if (!$tenantId && Auth::check()) {
                $user = Auth::user();
                // Many users have the school ID in their tenant_id column.
                // We should try to get the actual Stancl/Tenancy ID from the associated school.
                if ($user->tenant) {
                    $tenantId = $user->tenant->tenant_id;
                } else {
                    $tenantId = $user->tenant_id;
                }
            }

            if ($tenantId) {
                URL::defaults([
                    'tenant' => $tenantId,
                    'tenant_id' => $tenantId, // Set both to be safe
                ]);
            }
        }

        return $next($request);
    }
}
