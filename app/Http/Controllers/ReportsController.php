<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Illuminate\Support\Facades\Schema;
class ReportsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function reportsClassWiseTotalStudents()
    {
        // $classes = DB::table('classes')
        //         ->select(
        //             'classes.id',
        //             'classes.className',
        //             'schools.schoolName',
        //             DB::raw('(SELECT COUNT(*) FROM students WHERE students.class_id = classes.id) AS totalStudents')
        //         )
        //         ->join('schools', 'classes.school_id', '=', 'schools.id')
        //         ->get();
        
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $classes = DB::table('classes')
            ->select(
                'classes.id',
                'classes.className',
                'schools.schoolName',
                DB::raw("(SELECT COUNT(*) FROM students WHERE students.class_id = classes.id AND LOWER(students.status) = 'active') AS totalStudents"),
                DB::raw("(SELECT COUNT(*) FROM students WHERE students.class_id = classes.id AND LOWER(students.gender) = 'female' AND LOWER(students.status) = 'active') AS femaleStudents"),
                DB::raw("(SELECT COUNT(*) FROM students WHERE students.class_id = classes.id AND LOWER(students.gender) = 'male' AND LOWER(students.status) = 'active') AS maleStudents")
            )
            ->join('schools', 'classes.school_id', '=', 'schools.id')
            ->when($tenantId, function($q) use ($tenantId){ $q->where('classes.tenant_id', $tenantId); })
            ->get();




        return view('reportsClassWiseStudent',Compact('classes'));
    }
    
    public function classStudents($classID)
    {
        \Log::info('classStudents called with classID: ' . $classID);
        
        // Get the active academic year
        $academicTable = Schema::hasTable('academicyears') ? 'academicyears' : (Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
        $activeAcademicYear = DB::table($academicTable)
            ->where('is_active', 'yes')
            ->first();
            
        \Log::info('Active academic year: ' . json_encode($activeAcademicYear));
            
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $classWiseStudent = DB::table('classes')
                ->select(
                    'classes.id',
                    'classes.className',
                    'students.session as studentSession',
                    'students.studentName',
                    'students.grno',
                    'students.gender',
                    DB::raw('COALESCE(sections.sectionName, students.section) as section'),
                    'students.id as studentID',
                    'schools.schoolName')
                ->join('schools', 'classes.school_id', '=', 'schools.id')
                ->join('students', 'classes.id', '=', 'students.class_id')
                ->leftJoin('sections', 'students.section', '=', 'sections.id')
                ->where('classes.id', '=', $classID)
                ->when($tenantId, function($q) use ($tenantId){ $q->where('classes.tenant_id', $tenantId)->where('students.tenant_id', $tenantId); })
                ->where(function ($query) {
                    // Handle both status formats (active/Active)
                    $query->where('students.status', '=', 'active')
                          ->orWhere('students.status', '=', 'Active');
                })
                ->orderBy('students.studentName', 'asc')
                ->get();

        \Log::info('Found ' . $classWiseStudent->count() . ' students for class ' . $classID);
        \Log::info('Student data: ' . json_encode($classWiseStudent->take(2)));

        return view('classStudents',Compact('classWiseStudent'));
    }
    
    public function deleteStudent($studentID){
     
     DB::table('students')->where('id', $studentID)->delete();  
     
     
     return redirect()->back()->with('message', 'Student Deleted Successfully');
     
    }
    
    public function reportsClassWiseTotalFees(){
        
    // $classTotalFees = DB::table('classes')
    //     ->select(
    //         'classes.id',
    //         'classes.className',
    //         'classes.school_id',
    //         'schools.schoolName',
    //         DB::raw('(SELECT COUNT(*) FROM students WHERE students.class_id = classes.id) AS totalStudents'),
    //         DB::raw('(SELECT SUM(fee_value) FROM fees WHERE fees.class_name = classes.className AND fees.month = "March" AND fees.year = "2024") AS totalFees'),
    //         DB::raw('SUM(challans.total_fee) AS totalChallanFees')
    //     )
    //     ->join('schools', 'classes.school_id', '=', 'schools.id')
    //     ->leftJoin('challans', function ($join) {
    //         $join->on('classes.className', '=', 'challans.class_name')
    //              ->on('classes.school_id', '=', 'challans.school_id');
    //     })
    //     ->groupBy('classes.id', 'classes.className', 'classes.school_id', 'schools.schoolName')
    //     ->get();
    
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $classTotalFees = DB::table('classes')
            ->select(
                'classes.id',
                'classes.className',
                'classes.school_id',
                'schools.schoolName',
                DB::raw('(SELECT COUNT(*) FROM students WHERE students.class_id = classes.id AND students.session = "April 2024 to March 2025" ) AS totalStudents'),
                DB::raw('(SELECT SUM(fee_value) FROM fees WHERE fees.class_name = classes.className AND fees.session = "March 2024 to March 2025" AND fees.month = "March" AND fees.year = "2024") AS totalFees'),
                DB::raw('SUM(challans.total_fee) AS totalChallanFees'),
                DB::raw('SUM(CASE WHEN challans.type = "d" THEN challans.total_fee ELSE 0 END) AS totalChallanFeesD'),
                DB::raw('SUM(CASE WHEN challans.type = "c" THEN challans.total_fee ELSE 0 END) AS totalChallanFeesC')
            )
            ->join('schools', 'classes.school_id', '=', 'schools.id')
            ->leftJoin('challans', function ($join) use ($tenantId) {
                $join->on('classes.className', '=', 'challans.class_name')
                     ->on('classes.school_id', '=', 'challans.school_id');
                if ($tenantId) { $join->where('challans.tenant_id', $tenantId); }
            })
            ->when($tenantId, function($q) use ($tenantId){ $q->where('classes.tenant_id', $tenantId); })
            ->groupBy('classes.id', 'classes.className', 'classes.school_id', 'schools.schoolName')
            ->get();



        return view('reportsClasswiseFees',Compact('classTotalFees'));
    
    }
    
    public function reportsCollectiveFees(Request $request){
        
        // Optional filters from the search form
        $selectedSchoolId = $request->query('class_id'); // field name in the Blade form
        $fromMonth        = $request->query('fromMonth');
        $fromYear         = $request->query('fromYear');
        $toMonth          = $request->query('toMonth');
        $toYear           = $request->query('toYear');

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $challanQuery = DB::table('challans')
                ->when($tenantId, function($q) use ($tenantId){
                    $q->where('challans.tenant_id', $tenantId);
                })
                ->join('classes', function($join) {
                    $join->on('challans.class_name', '=', 'classes.className')
                         ->on('challans.school_id', '=', 'classes.school_id');
                })
                ->join('schools', 'classes.school_id', '=', 'schools.id') 
                ->select('schools.schoolName as school_name', 'classes.className', 'challans.month',  'challans.year') 
                ->selectRaw('SUM(COALESCE(challans.admission,0)) as total_admission,
                             SUM(COALESCE(challans.tution_fee,0)) as total_tuition_fee,
                             SUM(COALESCE(challans.idf,0)) as total_idf,
                             SUM(COALESCE(challans.csf,0)) as total_csf,
                             SUM(COALESCE(challans.rdfcdf,0)) as total_rdfcdf,
                             SUM(COALESCE(challans.breakage,0)) as total_breakage,
                             SUM(COALESCE(challans.misc,0)) as total_misc,
                             SUM(COALESCE(challans.clc,0)) as total_clc,
                             SUM(COALESCE(challans.it,0)) as total_it,
                             SUM(COALESCE(challans.security_fund,0)) as total_security_fund,
                             SUM(COALESCE(challans.exams,0)) as total_exams,
                             -- Prefer the challan\'s saved total_fee when available; else debit; else recompute from components
                             SUM(COALESCE(
                                 challans.total_fee,
                                 challans.debit,
                                 COALESCE(challans.tution_fee,0) + COALESCE(challans.admission,0) + COALESCE(challans.breakage,0) + COALESCE(challans.misc,0) + COALESCE(challans.clc,0)
                                 + COALESCE(challans.idf,0) + COALESCE(challans.exams,0) + COALESCE(challans.it,0) + COALESCE(challans.csf,0) + COALESCE(challans.rdfcdf,0) + COALESCE(challans.security_fund,0)
                             )) as total_fee');

        // Filter by selected school (if any)
        if (!empty($selectedSchoolId)) {
            $challanQuery->where('schools.id', $selectedSchoolId);
        }

        // Filter by month/year range (if fully specified)
        if (!empty($fromMonth) && !empty($fromYear) && !empty($toMonth) && !empty($toYear)) {
            $monthOrder = ['January','February','March','April','May','June','July','August','September','October','November','December'];

            $fromMonthIndex = array_search($fromMonth, $monthOrder);
            $toMonthIndex   = array_search($toMonth, $monthOrder);

            if ($fromMonthIndex !== false && $toMonthIndex !== false) {
                $periods = [];
                for ($year = (int)$fromYear; $year <= (int)$toYear; $year++) {
                    $startIdx = ($year == (int)$fromYear) ? $fromMonthIndex : 0;
                    $endIdx   = ($year == (int)$toYear) ? $toMonthIndex : count($monthOrder) - 1;
                    for ($idx = $startIdx; $idx <= $endIdx; $idx++) {
                        $periods[] = [
                            'month' => $monthOrder[$idx],
                            'year'  => (string)$year,
                        ];
                    }
                }

                if (!empty($periods)) {
                    $challanQuery->where(function($q) use ($periods) {
                        foreach ($periods as $p) {
                            $q->orWhere(function($qq) use ($p) {
                                $qq->where('challans.month', $p['month'])
                                   ->where('challans.year', $p['year']);
                            });
                        }
                    });
                }
            }
        }

        $challanData = $challanQuery
                ->groupBy('schools.id', 'schools.schoolName', 'classes.id', 'classes.className', 'challans.month', 'challans.year')
                ->get();
                
        $schoolList = DB::table('schools')
                    ->when($tenantId, function($q) use ($tenantId){ $q->where('id', $tenantId); })
                    ->get();
        $classList = DB::table('classes')
                    ->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })
                    ->get();

        return view('reportsCollectiveFees',Compact('challanData','schoolList','classList'));
    }
    
    public function classStudentsFees($classID){
        
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $classWiseStudent = DB::table('classes')
                ->select(
                    'classes.id',
                    'classes.className',
                    'students.studentName',
                    'students.id as studentID',
                    'schools.schoolName')
                ->join('schools', 'classes.school_id', '=', 'schools.id')
                ->join('students', 'classes.id', '=', 'students.class_id')
                ->where('classes.id', '=', $classID)
                ->when($tenantId, function($q) use ($tenantId){ $q->where('classes.tenant_id', $tenantId)->where('students.tenant_id', $tenantId); })
                ->where('students.status', '=', 'Active')
                ->get();


        return view('classStudentsFee',Compact('classWiseStudent'));
    }
    
    
    
    
    public function schools() {
        
        return view('schools');
    }
    
     public function addSchool(Request $request){

       $schoolId =  DB::table('schools')->insertGetId([
                'schoolName' => $request->schoolName,
                'schoolCity' => $request->schoolCity,
                'schoolAdminName' => $request->schoolAdminName,
                'schoolAdminEmail' => $request->schoolAdminEmail,
                'schoolAdminPassword' => Hash::make($request->schoolAdminPassword),
            ]);
            
            DB::table('users')->insert([
                'school_id' => $schoolId,
                'role' => 'admin',
                'name' => $request->schoolAdminName,
                'password' => Hash::make($request->schoolAdminPassword),
                'email' => $request->schoolAdminEmail,
            ]);


        return redirect()->back()->with('message', 'School Added Successfully');
    }
    
    public function getSchools(Request $request)
    {
         $columns = ['id', 'schoolName', 'schoolCity', 'schoolAdminName', 'schoolAdminEmail']; // Define columns to be selected
    
    // Sorting
    $sortColumnIndex = $request->input('order.0.column');
    $sortDirection = $request->input('order.0.dir');
    $sortBy = $columns[$sortColumnIndex];
    
    // Searching
    $searchValue = $request->input('search.value');
    
    $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
    $query = DB::table('schools')
                ->select('id', 'schoolName', 'schoolCity', 'schoolAdminName', 'schoolAdminEmail')
                ->when($tenantId, function($q) use ($tenantId){ $q->where('id', $tenantId); })
                ->where('schoolName', 'like', '%' . $searchValue . '%')
                ->orWhere('schoolCity', 'like', '%' . $searchValue . '%')
                ->orWhere('schoolAdminName', 'like', '%' . $searchValue . '%')
                ->orWhere('schoolAdminEmail', 'like', '%' . $searchValue . '%')
                ->orderBy($sortBy, $sortDirection);

    $totalData = $query->count();
    
    $perPage = $request->input('length');
    $page = $request->input('start') / $perPage + 1;
    
    $schools = $query->paginate($perPage, ['*'], 'page', $page);

    // Data format for DataTables
    $data = [];
    $serialNumber = ($page - 1) * $perPage + 1;
    foreach ($schools as $school) {
        $actionButton = sprintf('<button type="button" class="btn btn-primary edit-button" data-toggle="modal" data-target="#editModal" data-id="%s" data-name="%s" data-schoolCity="%s" data-schoolAdminName="%s" data-schoolAdminEmail="%s"> Edit </button>',
            $school->id,
            $school->schoolName,
            $school->schoolCity,
            $school->schoolAdminName,
            $school->schoolAdminEmail);

        $rowData = [
            'id' => $serialNumber,
            'schoolName' => $school->schoolName,
            'schoolCity' => $school->schoolCity,
            'schoolAdminName' => $school->schoolAdminName,
            'schoolAdminEmail' => $school->schoolAdminEmail,
            'action' => $actionButton
        ];
        $data[] = $rowData;
        $serialNumber++; // Increment for the next record
    }

    // JSON response
    $response = [
        'draw' => intval($request->input('draw')),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalData),
        'data' => $data,
    ];

    return response()->json($response);
    }
    
     public function academicYear() {
        
        return view('academicYear');
    }
    public function getAcademicYear(Request $request) {
        
        $columns = ['id', 'academicYear','is_active']; // Define columns to be selected
        $academicTable = Schema::hasTable('academicyears') ? 'academicyears' : (Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
        $totalData = DB::table($academicTable)->count(); // Assuming your table name is 'classes'

        // Sorting
        $sortColumnIndex = $request->input('order.0.column');
        $sortDirection = $request->input('order.0.dir');
        $sortBy = $columns[$sortColumnIndex];
        // Searching
        $searchValue = $request->input('search.value');
        $academicTable = Schema::hasTable('academicyears') ? 'academicyears' : (Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
        $classes = DB::table($academicTable)
            ->select('id', 'academicYear','is_active')
            ->where('academicYear', 'like', '%' . $searchValue . '%')
            ->where('is_active', 'like', '%' . $searchValue . '%')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($request->input('length'));

        // Data format for DataTables
        $data = [];
        foreach ($classes as $class) {
            $actionButton = '<button type="button" class="btn btn-primary edit-button" data-toggle="modal" data-target="#editModal" data-id="'.$class->id.'" data-name="'.$class->academicYear.'"data-is_active="'.$class->is_active.'"> Edit </button>';
            $rowData = [
                'id' => $class->id,
                'academicYear' => $class->academicYear,
                'is_active' => $class->is_active,
                'action' => $actionButton
            ];
            $data[] = $rowData;
        }

        // JSON response
        $response = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($classes->total()),
            'data' => $data,
        ];

        return response()->json($response);
    }
     
    public function updateAcademicYear() {
        return view('academicYear');
    }
    
    public function addAcademicYear(Request $request) {
         $academicTable = Schema::hasTable('academicyears') ? 'academicyears' : (Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
         $schoolId =  DB::table($academicTable)->insertGetId([
                'academicYear' => $request->academicYear,
                'is_active' => 'No',
            ]);
            
        return redirect()->back()->with('message', 'Academic Year Added Successfully');
    }
}
