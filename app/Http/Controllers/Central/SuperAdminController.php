<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\School;
use App\Models\students;
use App\Models\teachers;
use App\Models\fees;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\AuditLog;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalTenants = Tenant::count();

        $totals = [
            'students' => 0,
            'teachers' => 0,
            'fees_amount' => 0.0,
        ];

        foreach (Tenant::all() as $tenant) {
            tenancy()->initialize($tenant);
            try {
                $totals['students'] += (int) students::count();
                $totals['teachers'] += (int) teachers::count();
                $totals['fees_amount'] += (float) (fees::sum('amount') ?? 0);
            } finally {
                tenancy()->end();
            }
        }

        $premiumPrice = env('STRIPE_PREMIUM_PRICE');
        $goldPrice = env('STRIPE_GOLD_PRICE');

        $premiumTenants = School::when($premiumPrice, function ($q) use ($premiumPrice) {
            $q->whereHas('subscriptions', function ($s) use ($premiumPrice) {
                $s->whereNull('ends_at')
                  ->whereIn('stripe_status', ['active', 'trialing'])
                  ->where('stripe_price', $premiumPrice);
            });
        })->count();

        $goldTenants = School::when($goldPrice, function ($q) use ($goldPrice) {
            $q->whereHas('subscriptions', function ($s) use ($goldPrice) {
                $s->whereNull('ends_at')
                  ->whereIn('stripe_status', ['active', 'trialing'])
                  ->where('stripe_price', $goldPrice);
            });
        })->count();

        $schools = School::select('id', 'schoolName', 'tenant_id')->get();
        $tenants = Tenant::with('domains')->get();
        $onlineTenants = 0;
        foreach ($tenants as $t) {
            $tid = (string) $t->id;
            if (Cache::has('tenant_online_' . $tid)) {
                $onlineTenants++;
            }
        }

        // Build a complete schools list from tenants, falling back to tenant data when central School is missing
        $schoolsByTenantId = $schools->keyBy('tenant_id');
        $schoolsComplete = [];
        foreach ($tenants as $t) {
            $tenantId = (string) $t->id;
            $central = $schoolsByTenantId->get($tenantId);
            $schoolsComplete[] = [
                'tenant_id' => $tenantId,
                'name' => $central?->schoolName ?? ($t->data['name'] ?? $tenantId),
            ];
        }

        // Build initial activities snapshot (visible immediately on dashboard)
        $activitiesInitial = [];
        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);
            try {
                $tenantName = (string) $tenant->id;
                $recentStudents = students::orderByDesc('updated_at')
                    ->take(5)->get()->map(function ($s) use ($tenantName) {
                        return [
                            'tenant' => $tenantName,
                            'type' => 'student',
                            'action' => 'updated',
                            'at' => $s->updated_at,
                            'label' => $s->studentName,
                            'amount' => null,
                        ];
                    })->toArray();
                $recentTeachers = teachers::orderByDesc('updated_at')
                    ->take(5)->get()->map(function ($t) use ($tenantName) {
                        return [
                            'tenant' => $tenantName,
                            'type' => 'teacher',
                            'action' => 'updated',
                            'at' => $t->updated_at,
                            'label' => $t->teacherName ?? $t->teacher_name,
                            'amount' => null,
                        ];
                    })->toArray();
                $recentFees = fees::orderByDesc('updated_at')
                    ->take(5)->get()->map(function ($f) use ($tenantName) {
                        return [
                            'tenant' => $tenantName,
                            'type' => 'fee',
                            'action' => 'updated',
                            'at' => $f->updated_at,
                            'label' => $f->fee_name,
                            'amount' => $f->amount,
                        ];
                    })->toArray();
                $activitiesInitial = array_merge($activitiesInitial, $recentStudents, $recentTeachers, $recentFees);
            } finally {
                tenancy()->end();
            }
        }
        usort($activitiesInitial, function ($a, $b) { return ($b['at'] <=> $a['at']); });

        return view('superadmin.dashboard', [
            'totals' => $totals,
            'totalTenants' => $totalTenants,
            'premiumTenants' => $premiumTenants,
            'goldTenants' => $goldTenants,
            'schools' => $schools,
            'tenants' => $tenants,
            'schoolsComplete' => $schoolsComplete,
            'activitiesInitial' => $activitiesInitial,
            'onlineTenants' => $onlineTenants,
        ]);
    }

    public function activities(Request $request)
    {
        $days = (int) $request->query('days', 7);
        $activities = [];

        foreach (Tenant::all() as $tenant) {
            tenancy()->initialize($tenant);
            try {
                $tenantName = $tenant->getAttribute('id');

                $recentStudents = students::where('updated_at', '>=', now()->subDays($days))
                    ->orderByDesc('updated_at')
                    ->take(10)
                    ->get()
                    ->map(function ($s) use ($tenantName) {
                        return [
                            'tenant' => $tenantName,
                            'type' => 'student',
                            'action' => 'updated',
                            'at' => $s->updated_at,
                            'label' => $s->studentName,
                        ];
                    })->toArray();

                $recentTeachers = teachers::where('updated_at', '>=', now()->subDays($days))
                    ->orderByDesc('updated_at')
                    ->take(10)
                    ->get()
                    ->map(function ($t) use ($tenantName) {
                        return [
                            'tenant' => $tenantName,
                            'type' => 'teacher',
                            'action' => 'updated',
                            'at' => $t->updated_at,
                            'label' => $t->teacherName ?? $t->teacher_name,
                        ];
                    })->toArray();

                $recentFees = fees::where('updated_at', '>=', now()->subDays($days))
                    ->orderByDesc('updated_at')
                    ->take(10)
                    ->get()
                    ->map(function ($f) use ($tenantName) {
                        return [
                            'tenant' => $tenantName,
                            'type' => 'fee',
                            'action' => 'updated',
                            'at' => $f->updated_at,
                            'label' => $f->fee_name,
                            'amount' => $f->amount,
                        ];
                    })->toArray();

                $activities = array_merge($activities, $recentStudents, $recentTeachers, $recentFees);
            } finally {
                tenancy()->end();
            }
        }

        usort($activities, function ($a, $b) {
            return ($b['at'] <=> $a['at']);
        });

        return response()->json(['activities' => $activities]);
    }

    public function activitiesPage(Request $request)
    {
        $logs = \App\Models\AuditLog::with('user')->latest()->paginate(20);
        return view('superadmin.audit_logs', compact('logs'));
    }

    public function impersonate(Tenant $tenant)
    {
        $superId = Auth::id();
        session()->put('impersonator_id', $superId);
        session()->put('impersonator_tenant_id', $tenant->id);

        tenancy()->initialize($tenant);
        try {
            $target = User::where('role', 'admin')->first();
            if (!$target) {
                $target = User::where('role', 'teacher')->first();
            }
            if (!$target) {
                abort(404, 'No impersonation target found in tenant');
            }
            Auth::login($target, true);
            AuditLog::create([
                'user_id' => $superId,
                'event' => 'impersonate_start',
                'details' => ['tenant_id' => $tenant->id, 'target_user_id' => $target->id],
                'ip_address' => request()->ip(),
            ]);
        } finally {
            // Keep tenancy initialized; the impersonated session should stay in tenant context
        }

        return redirect()->route('home');
    }

    public function leaveImpersonation()
    {
        $impersonatorId = session()->pull('impersonator_id');
        session()->forget('impersonator_tenant_id');

        Auth::logout();
        tenancy()->end();

        if ($impersonatorId) {
            AuditLog::create([
                'user_id' => $impersonatorId,
                'event' => 'impersonate_end',
                'details' => ['tenant_id' => session('impersonator_tenant_id', 'unknown')],
                'ip_address' => request()->ip(),
            ]);
            Auth::loginUsingId((int) $impersonatorId, true);
            return redirect()->route('superadmin.dashboard');
        }

        return redirect()->route('home');
    }

    public function impersonateStart(Request $request)
    {
        $tenantId = (string) $request->input('tenant_id');
        $tenant = Tenant::findOrFail($tenantId);

        $superId = Auth::id();
        session()->put('impersonator_id', $superId);
        session()->put('impersonator_tenant_id', $tenant->id);

        tenancy()->initialize($tenant);
        try {
            $target = User::where('role', 'admin')->first();
            if (!$target) { $target = User::where('role', 'teacher')->first(); }
            if (!$target) { abort(404, 'No impersonation target found in tenant'); }
            Auth::login($target, true);
        } finally {
            // keep tenant context
        }
        return redirect()->route('home');
    }

    public function schoolsAll(Request $request)
    {
        $perPage = max(10, (int) $request->input('length', 25));
        $page = max(1, (int) $request->input('page', 1));
        $search = trim((string) $request->input('q', ''));

        $query = School::query()->select('id','schoolName','schoolCity','schoolAdminName','schoolAdminEmail','tenant_id','status');
        if ($search !== '') {
            $query->where(function($q) use ($search){
                $q->where('schoolName','like',"%$search%")
                  ->orWhere('schoolCity','like',"%$search%")
                  ->orWhere('schoolAdminName','like',"%$search%")
                  ->orWhere('schoolAdminEmail','like',"%$search%");
            });
        }
        $schools = $query->orderBy('schoolName','asc')->paginate($perPage, ['*'], 'page', $page);

        $tenantsList = Tenant::select('id')->orderBy('id','asc')->get();
        return view('superadmin.schools_all', [
            'schools' => $schools,
            'search' => $search,
            'tenantsList' => $tenantsList,
        ]);
    }

    public function linkTenant(Request $request, School $school)
    {
        $request->validate(['tenant_id' => 'required|string']);
        $tenantId = (string) $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return back()->with('errorMessage', 'Tenant not found');
        }
        $school->tenant_id = $tenantId;
        $school->tenant_id = $tenantId;
        $school->save();
        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => 'link_tenant',
            'details' => ['school_id' => $school->id, 'tenant_id' => $tenantId],
            'ip_address' => request()->ip(),
        ]);
        return back()->with('message', 'Tenant linked');
    }

    public function deleteSchool(School $school)
    {
        $id = $school->id;
        $name = $school->schoolName;
        $school->delete();
        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => 'delete_school',
            'details' => ['school_id' => $id, 'school_name' => $name],
            'ip_address' => request()->ip(),
        ]);
        return back()->with('message', 'School deleted');
    }

    public function tenantsAll(Request $request)
    {
        $perPage = max(10, (int) $request->input('length', 25));
        $page = max(1, (int) $request->input('page', 1));
        $search = trim((string) $request->input('q', ''));

        $query = Tenant::query()->with('domains');
        if ($search !== '') {
            $query->where(function($q) use ($search){
                $q->where('id','like',"%$search%")
                  ->orWhere('data->name','like',"%$search%");
            });
        }
        $tenants = $query->orderBy('id','asc')->paginate($perPage, ['*'], 'page', $page);

        return view('superadmin.tenants_all', [
            'tenants' => $tenants,
            'search' => $search,
        ]);
    }
}
