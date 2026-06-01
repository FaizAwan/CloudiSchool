<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrincipalRemarksController extends Controller
{
    public function __construct()
    {
        // Allow public access to index page; protect write actions
        $restricted = ['create','store','edit','update','destroy','toggleStatus'];
        $this->middleware('auth')->only($restricted);
        $this->middleware(function ($request, $next) use ($restricted) {
            $action = $request->route()->getActionMethod();
            if (in_array($action, $restricted, true)) {
                if (!in_array(auth()->user()->role ?? '', ['admin', 'superadmin'])) {
                    abort(403, 'Unauthorized access.');
                }
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the principal remarks.
     */
    public function index()
    {
        $this->ensureTable();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        
        $remarks = DB::table('principal_remarks')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->orderBy('sort_order')
            ->orderBy('percentage_min', 'desc')
            ->get();

        return view('principal_remarks.index', compact('remarks'));
    }

    /**
     * Show the form for creating a new principal remark.
     */
    public function create()
    {
        return view('principal_remarks.create');
    }

    /**
     * Store a newly created principal remark in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'percentage_min' => 'required|numeric|min:0|max:100',
            'percentage_max' => 'required|numeric|min:0|max:100|gte:percentage_min',
            'remark' => 'required|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->ensureTable();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        // Check for overlapping percentage ranges
        $overlapping = DB::table('principal_remarks')
            ->where('is_active', true)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->where(function ($query) use ($request) {
                $query->whereBetween('percentage_min', [$request->percentage_min, $request->percentage_max])
                    ->orWhereBetween('percentage_max', [$request->percentage_min, $request->percentage_max])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('percentage_min', '<=', $request->percentage_min)
                          ->where('percentage_max', '>=', $request->percentage_max);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()
                ->withErrors(['percentage' => 'Percentage range overlaps with existing active remark.'])
                ->withInput();
        }

        DB::table('principal_remarks')->insert([
            'tenant_id' => $tenantId,
            'percentage_min' => $request->percentage_min,
            'percentage_max' => $request->percentage_max,
            'remark' => $request->remark,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('principal-remarks.index')
            ->with('success', 'Principal remark created successfully.');
    }

    /**
     * Show the form for editing the specified principal remark.
     */
    public function edit($id)
    {
        $this->ensureTable();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $remark = DB::table('principal_remarks')
            ->where('id', $id)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->first();

        if (!$remark) {
            return redirect()->route('principal-remarks.index')
                ->with('error', 'Principal remark not found.');
        }

        return view('principal_remarks.edit', compact('remark'));
    }

    /**
     * Update the specified principal remark in storage.
     */
    public function update(Request $request, $id)
    {
        $this->ensureTable();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $validator = Validator::make($request->all(), [
            'percentage_min' => 'required|numeric|min:0|max:100',
            'percentage_max' => 'required|numeric|min:0|max:100|gte:percentage_min',
            'remark' => 'required|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for overlapping percentage ranges (excluding current record)
        $overlapping = DB::table('principal_remarks')
            ->where('is_active', true)
            ->where('id', '!=', $id)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->where(function ($query) use ($request) {
                $query->whereBetween('percentage_min', [$request->percentage_min, $request->percentage_max])
                    ->orWhereBetween('percentage_max', [$request->percentage_min, $request->percentage_max])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('percentage_min', '<=', $request->percentage_min)
                          ->where('percentage_max', '>=', $request->percentage_max);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()
                ->withErrors(['percentage' => 'Percentage range overlaps with existing active remark.'])
                ->withInput();
        }

        DB::table('principal_remarks')
            ->where('id', $id)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->update([
                'percentage_min' => $request->percentage_min,
                'percentage_max' => $request->percentage_max,
                'remark' => $request->remark,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'sort_order' => $request->sort_order ?? 0,
                'updated_at' => now(),
            ]);

        return redirect()->route('principal-remarks.index')
            ->with('success', 'Principal remark updated successfully.');
    }

    /**
     * Remove the specified principal remark from storage.
     */
    public function destroy($id)
    {
        $this->ensureTable();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $deleted = DB::table('principal_remarks')
            ->where('id', $id)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->delete();

        if ($deleted) {
            return redirect()->route('principal-remarks.index')
                ->with('success', 'Principal remark deleted successfully.');
        }

        return redirect()->route('principal-remarks.index')
            ->with('error', 'Principal remark not found.');
    }

    /**
     * Toggle the active status of a principal remark.
     */
    public function toggleStatus($id)
    {
        $this->ensureTable();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $remark = DB::table('principal_remarks')
            ->where('id', $id)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->first();

        if (!$remark) {
            return response()->json(['error' => 'Remark not found'], 404);
        }

        $newStatus = !$remark->is_active;

        DB::table('principal_remarks')
            ->where('id', $id)
            ->update([
                'is_active' => $newStatus,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
            'message' => 'Status updated successfully.'
        ]);
    }

    /**
     * Get the appropriate principal remark for a given percentage.
     */
    public static function getRemarkByPercentage($percentage)
    {
        if (!is_numeric($percentage)) {
            return null;
        }

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $remark = DB::table('principal_remarks')
            ->where('is_active', true)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->where('percentage_min', '<=', $percentage)
            ->where('percentage_max', '>=', $percentage)
            ->orderBy('sort_order')
            ->first();

        return $remark ? $remark->remark : null;
    }

    private function ensureTable(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('principal_remarks')) {
            \Illuminate\Support\Facades\Schema::create('principal_remarks', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
                $table->decimal('percentage_min', 5, 2)->default(0);
                $table->decimal('percentage_max', 5, 2)->default(100);
                $table->text('remark');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['is_active', 'sort_order', 'tenant_id']);
                $table->index(['percentage_min', 'percentage_max']);
            });
        }
    }
}