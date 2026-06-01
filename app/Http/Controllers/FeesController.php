<?php

namespace App\Http\Controllers;

use App\Models\fees;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Schema;
class FeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        try {
            $feeTypeList = DB::table('feetypes')
                ->where('status', 'active')
                ->get();
        } catch (\Exception $e) {
            $feeTypeList = collect([]);
        }
        return view('fees', compact('feeTypeList'));
    }
    
    public function updateFeeType(Request $request){
        // Validate the request
        $request->validate([
            'id' => 'required|exists:feetypes,id',
            'feeType' => 'required|string|max:255'
        ]);

        try {
            DB::table('feetypes')
                ->where('id', $request->id)
                ->update([
                    'name' => trim($request->feeType),
                    'updated_at' => now()
                ]);
            return redirect()->back()->with('message', 'Fee type updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update fee type. Please try again.']);
        }
    }

    public function addFeeType(Request $request){
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255|unique:feetypes,name'
        ]);

        try {
            DB::table('feetypes')->insert([
                'name' => trim($request->name),
                'description' => $request->description ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->back()->with('message', 'Fee type added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add fee type. Please try again.']);
        }
    }

    public function deleteFeeType($id)
    {
        try {
            DB::table('feetypes')
                ->where('id', $id)
                ->delete();
            return redirect()->back()->with('message', 'Fee type deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete fee type.']);
        }
    }

    
    public function feesManagement()
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? null) : null;

        try {
            $feeTypeList = DB::table('feetypes')->where('status', 'active')->get();
        } catch (\Throwable $e) { $feeTypeList = collect([]); }

        // Load academic years with robust fallbacks; do NOT clear on later errors
        $academicYears = collect();
        try {
            if (Schema::hasTable('academicyears')) {
                $q = DB::table('academicyears')->select('academicYear','is_active')->orderBy('academicYear');
                if ($tenantId && Schema::hasColumn('academicyears','tenant_id')) { $q->where('tenant_id',$tenantId); }
                if ($schoolId && Schema::hasColumn('academicyears','school_id')) { $q->orWhere('school_id',$schoolId); }
                $academicYears = $q->get();
                if ($academicYears->isEmpty()) {
                    $academicYears = DB::table('academicyears')->select('academicYear','is_active')->orderBy('academicYear')->get();
                }
            } elseif (Schema::hasTable('academicYears')) {
                $q = DB::table('academicYears')->select('academicYear','is_active')->orderBy('academicYear');
                if ($tenantId && Schema::hasColumn('academicYears','tenant_id')) { $q->where('tenant_id',$tenantId); }
                if ($schoolId && Schema::hasColumn('academicYears','school_id')) { $q->orWhere('school_id',$schoolId); }
                $academicYears = $q->get();
                if ($academicYears->isEmpty()) {
                    $academicYears = DB::table('academicYears')->select('academicYear','is_active')->orderBy('academicYear')->get();
                }
            } elseif (Schema::hasTable('academic_years')) {
                $q = DB::table('academic_years')->select(DB::raw('label as academicYear'),'is_active')->orderBy('label');
                if ($tenantId && Schema::hasColumn('academic_years','tenant_id')) { $q->where('tenant_id',$tenantId); }
                if ($schoolId && Schema::hasColumn('academic_years','school_id')) { $q->orWhere('school_id',$schoolId); }
                $academicYears = $q->get();
                if ($academicYears->isEmpty()) {
                    $academicYears = DB::table('academic_years')->select(DB::raw('label as academicYear'),'is_active')->orderBy('label')->get();
                }
            } else {
                $sessionValues = collect();
                if (Schema::hasTable('fees')) { $sessionValues = $sessionValues->merge(DB::table('fees')->select('session')->distinct()->pluck('session')); }
                if (Schema::hasTable('students')) { $sessionValues = $sessionValues->merge(DB::table('students')->select('session')->distinct()->pluck('session')); }
                if (Schema::hasTable('classes')) { $sessionValues = $sessionValues->merge(DB::table('classes')->select('session')->distinct()->pluck('session')); }
                $academicYears = $sessionValues->filter()->unique()->sort()->map(function($s){ return (object)['academicYear'=>$s,'is_active'=>null]; });
            }
            if ($academicYears->isEmpty()) {
                $merged = collect();
                if (Schema::hasTable('academicyears')) {
                    $merged = $merged->merge(DB::table('academicyears')->select('academicYear','is_active')->pluck('academicYear')->map(function($ay){ return ['academicYear'=>$ay,'is_active'=>null]; }));
                }
                if (Schema::hasTable('academicYears')) {
                    $merged = $merged->merge(DB::table('academicYears')->select('academicYear','is_active')->pluck('academicYear')->map(function($ay){ return ['academicYear'=>$ay,'is_active'=>null]; }));
                }
                if (Schema::hasTable('academic_years')) {
                    $merged = $merged->merge(DB::table('academic_years')->select('label','is_active')->pluck('label')->map(function($ay){ return ['academicYear'=>$ay,'is_active'=>null]; }));
                }
                $academicYears = collect($merged)->unique('academicYear')->sortBy('academicYear')->map(function($row){ return (object)$row; });
            }
        } catch (\Throwable $e) { /* keep $academicYears as collected so far */ }

        // Other datasets
        try {
            $classList = DB::table('classes')->when($tenantId, fn($q)=>$q->where('tenant_id',$tenantId))->get();
        } catch (\Throwable $e) { $classList = collect([]); }
        try {
            $schoolList = DB::table('schools')->when($tenantId, fn($q)=>$q->where('id',$tenantId))->get();
        } catch (\Throwable $e) { $schoolList = collect([]); }
        try {
            $feesList = DB::table('fees')
                ->leftJoin('feetypes', 'fees.fee_type_id', '=', 'feetypes.id')
                ->select('fees.*', 'feetypes.name as fee_type_name')
                ->when($tenantId, fn($q)=>$q->where('fees.tenant_id',$tenantId))
                ->orderBy('fees.created_at','desc')
                ->get();
        } catch (\Throwable $e) { $feesList = collect([]); }

        // Build fee groups safely
        $feeTypesByClass = [];
        $feeGroups = [];
        try {
            $allFeetypes = DB::table('feetypes')->get();
            $feetypesMap = [];
            foreach ($allFeetypes as $ft) { $feetypesMap[(string)$ft->id] = $ft->name; }
            $classIdToName = [];
            foreach ($classList as $c) { $classIdToName[(string)$c->id] = $c->className; }
            foreach ($feesList as $row) {
                $class = $row->class_name ?: ($classIdToName[(string)($row->class_id ?? '')] ?? null);
                if (!$class) { continue; }
                $month = ($row->month_name ?: $row->month) ?: null; if (!$month) { continue; }
                $year = trim((string)($row->year ?? '')); if ($year === '') { continue; }
                $key = trim((string)$class).'|'.trim((string)$month).'|'.$year;
                $name = $row->fee_name ?: (($idStr = trim((string)($row->fee_type_id ?? ''))) && isset($feetypesMap[$idStr]) ? $feetypesMap[$idStr] : $idStr);
                if (!$name) { continue; }
                if (!isset($feeGroups[$key])) { $feeGroups[$key] = ['session' => $row->session ?? null, 'values' => []]; }
                if (!array_key_exists($name, $feeGroups[$key]['values'])) {
                    $val = $row->fee_value ?? $row->amount; if ($val !== null) { $feeGroups[$key]['values'][$name] = (float)$val; }
                }
                if (empty($feeGroups[$key]['session']) && !empty($row->session)) { $feeGroups[$key]['session'] = $row->session; }
            }
        } catch (\Throwable $e) { /* leave feeGroups empty on error */ }

        return view('feesManagement', compact('feeTypeList','classList','schoolList','academicYears','feesList','feeTypesByClass','feeGroups'));
    }

    public function addFees(Request $request)
    {
        // Validate the request
        $request->validate([
            'class_id' => 'required|integer|exists:classes,id',
            'student_id' => 'nullable|integer|exists:students,id',
            'month' => 'required|string|max:50',
            'year' => 'required|integer|min:2020|max:2099',
            'academicYear' => 'required|string|max:255',
            'fee_types' => 'required|array',
            'fee_types.*' => 'nullable|numeric|min:0'
        ]);

        // Retrieve the input values from the form
        $classId = (int)$request->input('class_id');
        $studentId = $request->input('student_id');
        $month = $request->input('month');
        $year = (int)$request->input('year');
        $feeTypes = $request->input('fee_types');
        $academicYear = $request->input('academicYear');
        $selectedSchoolId = $request->input('school_id');

        try {
            // Resolve class and school reliably (avoid hardcoded school_id)
            $class = DB::table('classes')->where('id', $classId)->first();
            if (!$class) {
                return redirect()->back()->withErrors(['error' => 'Invalid class selected.']);
            }
            $schoolId = (int)($selectedSchoolId ?: ($class->school_id ?? 1));

            // Insert into fees table along with fee_type_id and fee_value
            foreach ($feeTypes as $feeTypeName => $feeValue) {
                if ($feeValue !== null && $feeValue !== '' && (float)$feeValue >= 0) {
                    $feeType = DB::table('feetypes')->where('name', $feeTypeName)->first();

                    DB::table('fees')->insert([
                        'tenant_id' => auth()->check() ? (auth()->user()->tenant_id ?? null) : null,
                        'student_id' => $studentId ? (int)$studentId : null,
                        'class_id' => $classId,
                        'class_name' => $class->className,
                        'fee_name' => $feeTypeName,
                        'amount' => (float)$feeValue,
                        'month' => $month,
                        'month_name' => $month,
                        'session' => $academicYear,
                        'year' => $year,
                        'fee_type_id' => $feeType ? $feeType->id : null,
                        'fee_value' => (float)$feeValue,
                        'school_id' => $schoolId,
                        'status' => 'unpaid',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            return redirect()->back()->with('message', 'Fees added successfully');
        } catch (\Exception $e) {
            // You can log the actual error for debugging
            \Log::error('Failed to add fees', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to add fees. Please try again.']);
        }
    }

    /**
     * Update a group of fees (by class, month, year) in bulk.
     */
    public function updateFeesGroup(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'month' => 'required|string',
            'year' => 'required|integer',
'session' => 'nullable|string',
            'school_id' => 'nullable|integer|exists:schools,id',
            'fee_values' => 'required|array',
            'fee_values.*' => 'nullable|numeric|min:0',
        ]);

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $className = $request->input('class_name');
        $month = $request->input('month');
        $year = (int)$request->input('year');
        $session = $request->input('session');
        $schoolId = $request->input('school_id');
        if (!$session) {
            $session = DB::table('academicYears')->where('is_active','yes')->value('academicYear');
        }
        $feeValues = $request->input('fee_values', []);

        // Resolve class id and school id if possible
        $class = DB::table('classes')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->where('className', $className)
            ->first();
        $classId = $class->id ?? null;
        $schoolId = $class->school_id ?? null;

        foreach ($feeValues as $feeName => $value) {
            $value = ($value === null || $value === '') ? null : (float)$value;
            $ft = DB::table('feetypes')->where('name', $feeName)->first();

            // Try to find existing fee row for this group and fee type
            $query = DB::table('fees')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                ->where('class_name', $className)
                ->where('month_name', $month)
                ->where('year', $year);
            if ($ft) {
                $query = $query->where(function($q) use ($ft, $feeName) {
                    $q->where('fee_type_id', $ft->id)
                      ->orWhere('fee_name', $feeName);
                });
            } else {
                $query = $query->where('fee_name', $feeName);
            }
            if ($schoolId) { $query = $query->where('school_id', $schoolId); }
            $existing = $query->orderByDesc('id')->first();

            if ($value === null) {
                // If value cleared and row exists, set to 0 but keep row to maintain structure
                if ($existing) {
                    DB::table('fees')->where('id', $existing->id)->update([
                        'fee_value' => 0,
                        'amount' => 0,
                        'updated_at' => now(),
                    ]);
                }
                continue;
            }

            $data = [
                'student_id' => null,
                'class_id' => $classId,
                'class_name' => $className,
                'fee_name' => $feeName,
                'amount' => $value,
                'month' => $month,
                'month_name' => $month,
                'session' => $session,
                'year' => $year,
                'fee_type_id' => $ft->id ?? null,
                'fee_value' => $value,
'school_id' => $schoolId ?: ($existing->school_id ?? null),
                'status' => $existing->status ?? 'unpaid',
                'updated_at' => now(),
            ];
            if ($tenantId) { $data['tenant_id'] = $tenantId; }

            if ($existing) {
                DB::table('fees')->where('id', $existing->id)->update($data);
            } else {
                $data['created_at'] = now();
                DB::table('fees')->insert($data);
            }
        }

        return redirect()->back()->with('message', 'Fees updated successfully');
    }

    public function deleteFeesGroup(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'month' => 'required|string',
            'year' => 'required|integer',
'session' => 'nullable|string',
            'school_id' => 'nullable|integer|exists:schools,id',
        ]);
        $className = $request->input('class_name');
        $month = $request->input('month');
        $year = (int)$request->input('year');
        $session = $request->input('session');
        $schoolId = $request->input('school_id');

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $q = DB::table('fees')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
            ->where('class_name', $className)
            ->where(function($q) use ($month){ $q->where('month_name',$month)->orWhere('month',$month); })
            ->where('year', $year);
        if ($session) { $q->where('session', $session); }
        if ($schoolId) { $q->where('school_id', $schoolId); }

        $deleted = $q->delete();
        return redirect()->back()->with('message', $deleted.' fee rows deleted for '.$className.' - '.$month.' '.$year);
    }

    public function duplicateFeesMonth(){

    $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

    $marchRecords = DB::table('fees')
                    ->select('class_id', 'fee_type_id', 'amount')
                    ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                    ->where('month', 'March')
                    ->where('year', 2024)
                    ->get();

    // Duplicate and update records for April 2024
    $aprilRecords = $marchRecords->map(function ($record) use ($tenantId) {
        return [
            'tenant_id' => $tenantId,
            'class_id' => $record->class_id,
            'month' => 'April',
            'year' => 2024,
            'fee_type_id' => $record->fee_type_id,
            'amount' => $record->amount,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    });

    // Insert duplicated records into the database
    DB::table('fees')->insert($aprilRecords->toArray());

    return "Records duplicated successfully.";

    }

    public function feeReceipt()
    {
        //
        return view('feeReceipt');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\fees  $fees
     * @return \Illuminate\Http\Response
     */
    public function show(fees $fees)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\fees  $fees
     * @return \Illuminate\Http\Response
     */
    public function edit(fees $fees)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\fees  $fees
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, fees $fees)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\fees  $fees
     * @return \Illuminate\Http\Response
     */
    public function destroy(fees $fees)
    {
        //
    }
}
