<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ParentsCrudController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        try { if (function_exists('tenant') && tenant()) { $tenantId = $tenantId ?: (string) tenant('id'); } } catch (\Throwable $e) {}
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        $schools = DB::table('schools')
            ->select('id','schoolName')
            ->when($tenantId, fn($q)=>$q->where('id',$tenantId))
            ->when(!$tenantId && $schoolId, fn($q)=>$q->where('id',$schoolId))
            ->when(!$tenantId && !$schoolId, fn($q)=>$q->whereRaw('1=0'))
            ->orderBy('schoolName')
            ->get();
        return view('parents_crud.index', compact('schools'));
    }

    public function list(Request $request)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        try { if (function_exists('tenant') && tenant()) { $tenantId = $tenantId ?: (string) tenant('id'); } } catch (\Throwable $e) {}
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        $q = DB::table('parents')
            ->leftJoin('schools','parents.school_id','=','schools.id')
            ->select(
                'parents.id','parents.parentName','parents.is_commandercityschool_employee','parents.phone','parents.address',
                'schools.schoolName',
                DB::raw('(SELECT COUNT(*) FROM students WHERE students.parent_id = parents.id) AS student_count')
            )
            ->when($tenantId, fn($qq)=>$qq->where('parents.tenant_id',$tenantId))
            ->when(!$tenantId && $schoolId, fn($qq)=>$qq->where('parents.school_id',$schoolId))
            ->when(!$tenantId && !$schoolId, fn($qq)=>$qq->whereRaw('1=0'));
        $search = $request->input('search.value');
        if ($search) {
            $q->where(function($qq) use ($search){
                $qq->where('parents.parentName','like',"%$search%")
                   ->orWhere('parents.phone','like',"%$search%")
                   ->orWhere('parents.address','like',"%$search%")
                   ->orWhere('parents.is_commandercityschool_employee','like',"%$search%")
                   ->orWhere('schools.schoolName','like',"%$search%");
            });
        }
        $total = $q->count();
        $start = (int)$request->input('start',0);
        $rows = $q->orderBy('parents.id','desc')
            ->offset($start)
            ->limit((int)$request->input('length',10))
            ->get();
        $data = [];
        $i = 0;
        foreach ($rows as $r) {
            $actions = '<div class="actions d-inline-flex align-items-center text-nowrap" style="gap:6px;">'
                .'<a href="'.route('parents.view',$r->id).'" class="btn btn-sm btn-secondary" title="View" target="_blank" rel="noopener"><i class="bi bi-eye"></i></a>'
                .'<button type="button" class="btn btn-sm btn-primary p-edit" title="Edit" data-bs-toggle="modal" data-bs-target="#pEditModal" '
                .'data-id="'.$r->id.'" data-name="'.e($r->parentName).'" data-emp="'.$r->is_commandercityschool_employee.'" data-phone="'.e($r->phone).'" data-address="'.e($r->address).'" data-school="'.e($r->schoolName).'" ><i class="bi bi-pencil"></i></button>'
                .'<a href="'.route('parents.destroy',['id'=>$r->id]).'" class="btn btn-sm btn-danger p-delete" title="Delete"><i class="bi bi-trash"></i></a>'
                .'</div>';
            $data[] = [
                'sn' => $start + (++$i),
                'parent' => e($r->parentName),
                'children' => (int)$r->student_count,
                'employee' => e($r->is_commandercityschool_employee),
                'phone' => e($r->phone),
                'school' => e($r->schoolName ?? ''),
                'address' => e($r->address),
                'action' => $actions,
            ];
        }
        return response()->json([
            'draw' => (int)$request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|integer',
            'parentName' => 'required|string|max:255',
            'is_commandercityschool_employee' => 'required|in:Yes,No',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        $isSuperadmin = auth()->check() ? ((auth()->user()->role ?? '') === 'superadmin') : false;
        if (session()->has('impersonator_id')) { $isSuperadmin = true; }
        if (!$isSuperadmin) {
            $allowedSchoolId = $tenantId ?: $schoolId;
            if (!$allowedSchoolId) { return back()->with('errorMessage','Unauthorized'); }
            if ((int)$request->school_id !== (int)$allowedSchoolId) {
                return back()->with('errorMessage','Invalid school selection');
            }
        }
        DB::table('parents')->insert([
            'tenant_id' => $tenantId,
            'school_id' => (int)$request->school_id,
            'parentName' => trim($request->parentName),
            'is_commandercityschool_employee' => $request->is_commandercityschool_employee,
            'phone' => trim((string)$request->phone),
            'address' => trim((string)$request->address),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('message','Parent added');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'parentName' => 'required|string|max:255',
            'is_commandercityschool_employee' => 'required|in:Yes,No',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        try { if (function_exists('tenant') && tenant()) { $tenantId = $tenantId ?: (string) tenant('id'); } } catch (\Throwable $e) {}
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        $isSuperadmin = auth()->check() ? ((auth()->user()->role ?? '') === 'superadmin') : false;
        if (session()->has('impersonator_id')) { $isSuperadmin = true; }
        $q = DB::table('parents')->where('id',(int)$id);
        if (!$isSuperadmin) {
            if ($tenantId) { $q->where('tenant_id',$tenantId); }
            elseif ($schoolId) { $q->where('school_id',$schoolId); }
            else { return back()->with('errorMessage','Unauthorized'); }
        }
        $affected = $q->update([
            'parentName' => trim($request->parentName),
            'is_commandercityschool_employee' => $request->is_commandercityschool_employee,
            'phone' => trim((string)$request->phone),
            'address' => trim((string)$request->address),
            'updated_at' => now(),
        ]);
        if ($request->ajax()) { return response()->json(['ok'=>true,'updated'=>$affected>0]); }
        return back()->with($affected? 'message':'errorMessage', $affected? 'Parent updated':'No changes made');
    }

    public function destroy(Request $request, $id)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        try { if (function_exists('tenant') && tenant()) { $tenantId = $tenantId ?: (string) tenant('id'); } } catch (\Throwable $e) {}
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        $isSuperadmin = auth()->check() ? ((auth()->user()->role ?? '') === 'superadmin') : false;
        if (session()->has('impersonator_id')) { $isSuperadmin = true; }
        $q = DB::table('parents')->where('id',(int)$id);
        if (!$isSuperadmin) {
            if ($tenantId) { $q->where('tenant_id',$tenantId); }
            elseif ($schoolId) { $q->where('school_id',$schoolId); }
            else { return back()->with('errorMessage','Unauthorized'); }
        }
        $deleted = $q->delete();
        if ($request->ajax()) { return response()->json(['ok'=>(bool)$deleted]); }
        return back()->with('message','Parent deleted');
    }

    public function view($id)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;
        $isSuperadmin = auth()->check() ? ((auth()->user()->role ?? '') === 'superadmin') : false;
        $parent = DB::table('parents')
            ->leftJoin('schools','parents.school_id','=','schools.id')
            ->select('parents.*','schools.schoolName')
            ->where('parents.id',(int)$id)
            ->when(!$isSuperadmin && $tenantId, fn($q)=>$q->where('parents.tenant_id',$tenantId))
            ->when(!$isSuperadmin && !$tenantId && $schoolId, fn($q)=>$q->where('parents.school_id',$schoolId))
            ->when(!$isSuperadmin && !$tenantId && !$schoolId, fn($q)=>$q->whereRaw('1=0'))
            ->first();
        if (!$parent) { return redirect()->route('parents')->with('errorMessage','Parent not found'); }
        $children = DB::table('students')
            ->leftJoin('classes','students.class_id','=','classes.id')
            ->select('students.*','classes.className')
            ->where('students.parent_id',(int)$id)
            ->orderBy('students.studentName','asc')->get();
        return view('parent_view', compact('parent','children'));
    }
}
