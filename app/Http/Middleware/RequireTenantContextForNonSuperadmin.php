<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use function Stancl\Tenancy\tenancy;

class RequireTenantContextForNonSuperadmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role !== 'superadmin') {
                $currentTenantId = null;
                try {
                    $currentTenantId = function_exists('tenant') ? (tenant('id') ?? null) : null;
                } catch (\Throwable $e) {
                }
                $impersonatedTenantId = session()->get('impersonator_tenant_id');
                $userTenantId = $user->tenant_id ?? null;

                $targetTenantId = $currentTenantId ?: $impersonatedTenantId ?: $userTenantId;

                if (!$currentTenantId && $targetTenantId && class_exists(Tenant::class) && function_exists('tenancy')) {
                    try {
                        // First try finding directly (if tenant_id refers to actual Tenant ID)
                        $tenant = Tenant::find((string) $targetTenantId);

                        // If not found, check if it refers to a School ID and get its tenant_id
                        if (!$tenant && $user->tenant) {
                            $actualTenantId = $user->tenant->tenant_id;
                            if ($actualTenantId) {
                                $tenant = Tenant::find((string) $actualTenantId);
                            }
                        }

                        if ($tenant) {
                            tenancy()->initialize($tenant);
                        }
                    } catch (\Throwable $e) {
                    }
                }

                if ($request->is('schools') || $request->is('fetch-branch-details/*') || $request->is('save-branch-details') || $request->is('exam-reports*') || $request->is('student-exams*') || $request->is('question-bank*') || $request->is('exam-schedule*')) {
                    return $next($request);
                }

                if (!($currentTenantId ?: (session()->get('impersonator_tenant_id') ?? $userTenantId))) {
                    abort(403);
                }
            }
        }

        return $next($request);
    }
}

