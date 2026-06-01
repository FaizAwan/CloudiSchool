<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PlanLimits;
use App\Models\School;
use Stancl\Tenancy\Database\Models\Tenant;
use Illuminate\Support\Facades\DB;

class EnforcePlanLimits
{
    /**
     * Enforce plan limits for specific actions, without editing existing routes.
     * We detect the route name and enforce where needed (e.g., 'addStudent').
     */
    public function handle(Request $request, Closure $next)
    {
        // Only act when in tenant context
        if (!function_exists('tenant') || !tenant()) {
            return $next($request);
        }

        $routeName = optional($request->route())->getName();

        // Example: enforce student creation limit on the 'addStudent' route
        if ($routeName === 'addStudent') {
            $tenantId = (string) tenant('id');
            $school = School::where('tenant_id', $tenantId)->first();
            if ($school) {
                $limits = app(PlanLimits::class)->forSchool($school);
                $max = (int) ($limits['max_students'] ?? PHP_INT_MAX);

                // Count current students in tenant DB
                $count = DB::table('students')->count();
                if ($count >= $max) {
                    return response()->view('tenant.limit_reached', [
                        'resource' => 'students',
                        'limit' => $max,
                    ], 403);
                }
            }
        }

        // Enforce teacher creation limit on 'addTeacher'
        if ($routeName === 'addTeacher') {
            $tenantId = (string) tenant('id');
            $school = School::where('tenant_id', $tenantId)->first();
            if ($school) {
                $limits = app(PlanLimits::class)->forSchool($school);
                $max = (int) ($limits['max_teachers'] ?? PHP_INT_MAX);

                $count = DB::table('teachers')->count();
                if ($count >= $max) {
                    return response()->view('tenant.limit_reached', [
                        'resource' => 'teachers',
                        'limit' => $max,
                    ], 403);
                }
            }
        }

        // Enforce exams creation on 'exams.store'
        if ($routeName === 'exams.store') {
            $tenantId = (string) tenant('id');
            $school = School::where('tenant_id', $tenantId)->first();
            if ($school) {
                $limits = app(PlanLimits::class)->forSchool($school);
                $max = (int) ($limits['max_exams'] ?? PHP_INT_MAX);

                $count = DB::table('exams')->count();
                if ($count >= $max) {
                    return response()->view('tenant.limit_reached', [
                        'resource' => 'exams',
                        'limit' => $max,
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
