<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AcademicYearController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        // Silent migration: Standardize existing labels to YYYY-YYYY format
        try {
            $table = $this->table();
            $col = Schema::hasColumn($table, 'academicYear') ? 'academicYear' : 'label';
            DB::table($table)->get()->each(function($ay) use ($table, $col) {
                if ($ay->start_date && $ay->end_date) {
                    $newLabel = Carbon::parse($ay->start_date)->format('Y') . '-' . Carbon::parse($ay->end_date)->format('Y');
                    if (($ay->$col ?? '') !== $newLabel) {
                        DB::table($table)->where('id', $ay->id)->update([$col => $newLabel]);
                    }
                }
            });
        } catch (\Throwable $e) {}
        
        return view('academic_years.index');
    }

    private function table(): string
    {
        if (Schema::hasTable('academicyears')) { return 'academicyears'; }
        if (Schema::hasTable('academic_years')) { return 'academic_years'; }
        if (Schema::hasTable('academicYears')) { return 'academicYears'; }
        return 'academicyears';
    }

    public function list(Request $request)
    {
        $table = $this->table();
        $query = DB::table($table);
        // Build select adapting to existing columns
        $hasLabel = Schema::hasColumn($table, 'label');
        $hasAcademicYear = Schema::hasColumn($table, 'academicYear');
        if ($hasLabel) {
            $query->select('id','label','start_date','end_date','is_active');
        } else {
            // academicYears: compute label from existing columns
            $query->select('id','start_date','end_date','is_active')
                  ->addSelect(DB::raw(
                      ($hasAcademicYear
                        ? 'academicYear'
                        : "CONCAT(YEAR(start_date),'-',YEAR(end_date))")
                      . ' as label'
                  ));
        }
        $query->orderBy('start_date','desc');
        $search = $request->input('search.value');
        if ($search) {
            $query->where(function($q) use ($search){
                $q->where('label','like',"%$search%")
                  ->orWhere('is_active','like',"%$search%");
            });
        }
        $total = $query->count();
        $rows = $query->offset((int)$request->input('start',0))
            ->limit((int)$request->input('length',10))
            ->get();
        $data = [];
        foreach ($rows as $r) {
            $fromMonth = $r->start_date ? Carbon::parse($r->start_date)->format('F') : '';
            $fromYear  = $r->start_date ? Carbon::parse($r->start_date)->format('Y') : '';
            $toMonth   = $r->end_date ? Carbon::parse($r->end_date)->format('F') : '';
            $toYear    = $r->end_date ? Carbon::parse($r->end_date)->format('Y') : '';
            $statusBadge = '<span class="badge bg-'.($r->is_active==='yes'?'success':($r->is_active==='closed'?'danger':'secondary')).'">'.ucfirst($r->is_active).'</span>';
            $actions = '<button type="button" class="btn btn-sm btn-primary ay-edit" data-bs-toggle="modal" data-bs-target="#ayEditModal" '
                .'data-id="'.$r->id.'" data-label="'.$r->label.'" data-frommonth="'.$fromMonth.'" data-fromyear="'.$fromYear.'" data-tomonth="'.$toMonth.'" data-toyear="'.$toYear.'" data-is_active="'.$r->is_active.'">Edit</button> '
                .'<button type="button" class="btn btn-sm btn-danger ay-delete" data-id="'.$r->id.'">Delete</button>';
            $displayLabel = ($r->start_date && $r->end_date) 
                ? (Carbon::parse($r->start_date)->format('Y') . '-' . Carbon::parse($r->end_date)->format('Y'))
                : $r->label;
            $data[] = [
                'label' => e($displayLabel),
                'is_active' => $statusBadge,
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
            'fromMonth' => 'required|string',
            'fromYear' => 'required|integer',
            'toMonth' => 'required|string',
            'toYear' => 'required|integer',
            'is_active' => 'required|in:yes,no,closed',
        ]);
        $table = $this->table();
        $monthMap = [
            'January'=>1,'February'=>2,'March'=>3,'April'=>4,'May'=>5,'June'=>6,
            'July'=>7,'August'=>8,'September'=>9,'October'=>10,'November'=>11,'December'=>12,
        ];
        $fm = $monthMap[$request->fromMonth] ?? null;
        $tm = $monthMap[$request->toMonth] ?? null;
        if (!$fm || !$tm) { return back()->with('errorMessage','Invalid month'); }
        $fromDate = Carbon::createFromFormat('n-Y', $fm.'-'.$request->fromYear)->startOfMonth();
        $toDate   = Carbon::createFromFormat('n-Y', $tm.'-'.$request->toYear)->endOfMonth();
        $label = $request->fromYear.'-'.$request->toYear;
        // Insert adapting to table columns
        if ($table === 'academic_years') {
            DB::table($table)->insert([
                'label' => $label,
                'start_date' => $fromDate->toDateString(),
                'end_date' => $toDate->toDateString(),
                'is_active' => $request->is_active,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table($table)->insert([
                'academicYear' => $label,
                'start_date' => $fromDate->toDateString(),
                'end_date' => $toDate->toDateString(),
                'is_active' => $request->is_active,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return back()->with('message','Academic Year created');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fromMonth' => 'required|string',
            'fromYear' => 'required|integer',
            'toMonth' => 'required|string',
            'toYear' => 'required|integer',
            'is_active' => 'required|in:yes,no,closed',
        ]);
        $table = $this->table();
        $monthMap = [
            'January'=>1,'February'=>2,'March'=>3,'April'=>4,'May'=>5,'June'=>6,
            'July'=>7,'August'=>8,'September'=>9,'October'=>10,'November'=>11,'December'=>12,
        ];
        $fm = $monthMap[$request->fromMonth] ?? null;
        $tm = $monthMap[$request->toMonth] ?? null;
        if (!$fm || !$tm) { return back()->with('errorMessage','Invalid month'); }
        $fromDate = Carbon::createFromFormat('n-Y', $fm.'-'.$request->fromYear)->startOfMonth();
        $toDate   = Carbon::createFromFormat('n-Y', $tm.'-'.$request->toYear)->endOfMonth();
        $label = $request->fromYear.'-'.$request->toYear;
        if ($table === 'academic_years') {
            $affected = DB::table($table)->where('id',(int)$id)->update([
                'label' => $label,
                'start_date' => $fromDate->toDateString(),
                'end_date' => $toDate->toDateString(),
                'is_active' => $request->is_active,
                'updated_at' => now(),
            ]);
        } else {
            $affected = DB::table($table)->where('id',(int)$id)->update([
                'academicYear' => $label,
                'start_date' => $fromDate->toDateString(),
                'end_date' => $toDate->toDateString(),
                'is_active' => $request->is_active,
                'updated_at' => now(),
            ]);
        }
        if ($request->ajax()) {
            return response()->json(['ok'=>true,'updated'=>$affected>0]);
        }
        return back()->with($affected? 'message':'errorMessage', $affected? 'Academic Year updated':'No changes made');
    }

    public function destroy(Request $request, $id)
    {
        $deleted = DB::table($this->table())->where('id',(int)$id)->delete();
        if ($request->ajax()) {
            return response()->json(['ok'=> (bool)$deleted]);
        }
        return back()->with('message','Academic Year deleted');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate(['is_active'=>'required|in:yes,no,closed']);
        DB::table($this->table())->where('id',(int)$id)->update([
            'is_active'=>$request->is_active,
            'updated_at'=>now(),
        ]);
        return response()->json(['ok'=>true]);
    }
}
