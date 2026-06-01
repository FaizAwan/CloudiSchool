<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the new Add Section interface
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id ?? null;
        
        // Fetch all classes for the dropdown
        $classes = DB::table('classes')
            ->leftJoin('schools', 'classes.school_id', '=', 'schools.id')
            ->select('classes.id', 'classes.className', 'schools.schoolName')
            ->when($tenantId, function($q) use ($tenantId) { 
                $q->where('classes.tenant_id', $tenantId); 
            })
            ->orderBy('schools.schoolName')
            ->orderBy('classes.className')
            ->get();

        return view('sections_manage', compact('classes'));
    }

    /**
     * Get sections for a class (JSON)
     */
    public function listByClass($classId)
    {
        $tenantId = auth()->user()->tenant_id ?? null;
        $sections = DB::table('sections')
            ->where('class_id', $classId)
            ->when($tenantId, function($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
            ->orderBy('sectionName')
            ->get();
        return response()->json($sections);
    }

    /**
     * Legacy store method (kept for compatibility with class management modal)
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|integer',
            'sectionName' => 'required|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id ?? null;
        
        // Multi-tenant check
        $classExists = DB::table('classes')->where('id', $request->class_id)
            ->when($tenantId, function($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
            ->exists();
        
        if (!$classExists) {
            return response()->json(['ok' => false, 'message' => 'Invalid class'], 403);
        }

        DB::table('sections')->insert([
            'class_id' => $request->class_id,
            'sectionName' => trim($request->sectionName),
            'tenant_id' => $tenantId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Update an existing section
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sectionName' => 'required|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id ?? null;
        
        $affected = DB::table('sections')
            ->where('id', $id)
            ->when($tenantId, function($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
            ->update([
                'sectionName' => trim($request->sectionName),
                'updated_at' => now(),
            ]);

        return response()->json(['ok' => $affected > 0]);
    }

    /**
     * Delete a section
     */
    public function destroy($id)
    {
        $tenantId = auth()->user()->tenant_id ?? null;
        
        $deleted = DB::table('sections')
            ->where('id', $id)
            ->when($tenantId, function($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
            ->delete();

        return response()->json(['ok' => $deleted > 0]);
    }
}
