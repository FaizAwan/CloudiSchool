<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class TenantsAdminController extends Controller
{
    public function index()
    {
        $schools = School::orderByDesc('id')->paginate(20);
        return view('central.tenants.index', compact('schools'));
    }

    public function suspend(School $school)
    {
        $school->update(['status' => 'suspended']);
        // Optional: also set tenant data flag
        try {
$tenant = \App\Models\Tenant::find($school->tenant_id);
            if ($tenant) $tenant->put('suspended', true);
        } catch (\Throwable $e) {}
        return back()->with('status', 'Suspended '.$school->schoolName);
    }

    public function resume(School $school)
    {
        $school->update(['status' => 'active']);
        try {
$tenant = \App\Models\Tenant::find($school->tenant_id);
            if ($tenant) $tenant->put('suspended', false);
        } catch (\Throwable $e) {}
        return back()->with('status', 'Resumed '.$school->schoolName);
    }
}
