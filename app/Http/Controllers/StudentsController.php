<?php

namespace App\Http\Controllers;

use App\Models\students;
use Illuminate\Http\Request;
use DB;
use Auth;

class StudentsController extends Controller
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

    public function studentsWithSameGRno()
    {
        $tenantId = auth()->user()->tenant_id ?? null;
        try {
            if (function_exists('tenant') && tenant()) {
                $tenantId = $tenantId ?: (string) tenant('id');
            }
        } catch (\Throwable $e) {
        }
        $duplicateStudents = DB::table('students')->where('session', '=', 'April 2024 to March 2025')->where('status', '=', 'active')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('students.tenant_id', $tenantId);
            })
            ->select(DB::raw("TRIM(grno) as grno"), DB::raw('COUNT(*) as grno_count'))
            ->groupBy('grno')
            ->having('grno_count', '>', 1)
            ->get();


        return view('studentsWithSameGRno', Compact('duplicateStudents'));
    }

    public function studentsListGRno()
    {
        // List students ordered by numeric GRNO, with class, school, and parent details
        $tenantId = auth()->user()->tenant_id ?? null;
        try {
            if (function_exists('tenant') && tenant()) {
                $tenantId = $tenantId ?: (string) tenant('id');
            }
        } catch (\Throwable $e) {
        }
        $students = DB::table('students')
            ->join('schools', 'students.school_id', '=', 'schools.id')
            ->join('parents', 'students.parent_id', '=', 'parents.id')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->select(
                'students.*',
                'schools.schoolName as schoolName',
                'parents.parentName as parentName',
                'classes.className as className'
            )
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('students.tenant_id', $tenantId);
            })
            ->orderByRaw('CAST(students.grno AS UNSIGNED) DESC')
            ->get();

        return view('grno', Compact('students'));
    }
    public function index()
    {
        $tenantId = auth()->user()->tenant_id ?? null;
        $userEmail = Auth::user()->email;
        $userRole = Auth::user()->role;

        $classList = DB::table('classes')->when($tenantId, function ($q) use ($tenantId) {
            $q->where('classes.tenant_id', $tenantId);
        })->select('id', 'className')->get();
        $allClasses = $classList;

        // Optimize parent list: only fetch what's needed for the 25 most recent for the dropdown, 
        // real search will be handled by Select2 later if needed. For now, just optimize the collection.
        $parentList = DB::table('parents')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('parents.tenant_id', $tenantId);
            })
            ->select('id', 'parentName')
            ->limit(100)
            ->get();

        $schoolList = DB::table('schools')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('id', $tenantId);
            })
            ->select('id', 'schoolName')
            ->get();

        return view('students_enhanced', Compact('classList', 'schoolList', 'allClasses', 'parentList'));
    }
    public function addStudent(Request $request)
    {

        $className = DB::table('classes')->where('id', '=', $request->class_id)->first();
        $tenantId = auth()->user()->tenant_id ?? null;
        $student = DB::table('students')
            ->where('grno', '=', $request->grno)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->exists();

        if ($student) {
            return redirect()->back()->with('message', 'GRNO exists for your school. Please use the next available GR number.');
        }

        // Validate studentName and grno
        if (empty($request->studentName) && empty($request->grno)) {
            return redirect()->back()->with('message', 'Both Student Name and GRNO cannot be empty.');
        }

        DB::table('students')->insert([
            'class_id' => $request->class_id,
            'session' => 'April 2024 to March 2025',
            'school_id' => $request->school_id,
            'parent_id' => $request->parentID,
            'grno' => $request->grno,
            'status' => 'active',
            'gender' => $request->gender,
            'studentName' => $request->studentName,
            'section' => $request->section,
            'tenant_id' => auth()->user()->tenant_id ?? null
        ]);

        return redirect()->back();
    }
    public function editStudent($studentID)
    {

        $studentDetail = DB::table('students')
            ->leftJoin('parents', 'students.parent_id', '=', 'parents.id')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->where('students.id', '=', $studentID)
            ->select('students.*', 'parents.parentName', 'parents.id as parentId', 'classes.className')
            ->first();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $schoolList = DB::table('schools')
            ->when($tenantId, fn($q) => $q->where('id', $tenantId))
            ->get();
        $parentList = DB::table('parents')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();
        $classList = DB::table('classes')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();
        $academicTable = \Illuminate\Support\Facades\Schema::hasTable('academicyears') ? 'academicyears' : (\Illuminate\Support\Facades\Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
        $academicYears = DB::table($academicTable)->orderBy(\Illuminate\Support\Facades\Schema::hasColumn($academicTable, 'academicYear') ? 'academicYear' : 'label', 'desc')->get();


        return view('editStudent', Compact('studentDetail', 'schoolList', 'parentList', 'classList', 'academicYears'));
    }


    public function updateStudent(Request $request)
    {

        $affected = DB::table('students')
            ->where('id', $request->id)
            ->update([
                'school_id' => $request->school_id,
                'gender' => $request->gender,
                'grno' => $request->grno,
                'studentName' => $request->studentName,
                'parent_id' => $request->parent_id,
                'status' => $request->status,
                'class_id' => $request->class_id,
                'section' => $request->section,
                'session' => $request->session
            ]);
        return redirect('students');
    }

    public function getStudents(Request $request)
    {


        $userEmail = Auth::user()->email;
        $userRole = Auth::user()->role;


        if ($userRole == 'teacher') {

            $userEmail = Auth::user()->email;
            $userRole = Auth::user()->role;
            $searchTeacher = DB::table('teachers')->where('email', '=', $userEmail)->first();
            $classID = $searchTeacher->class_id;
            $className = $searchTeacher->className;

            $columns = ['id', 'session', 'className', 'grno', 'status', 'gender', 'schoolName', 'studentName', 'parentName']; // Define columns to be selected
            $sortColumnIndex = $request->input('order.0.column');
            $sortDirection = $request->input('order.0.dir');
            $sortBy = $columns[$sortColumnIndex];

            // Searching
            $searchValue = $request->input('search.value');

            // Get total count of records
            $tenantId = auth()->user()->tenant_id ?? null;
            $totalData = DB::table('students')
                ->join('parents', 'students.parent_id', '=', 'parents.id')
                ->join('schools', 'students.school_id', '=', 'schools.id')
                ->join('classes', 'students.class_id', '=', 'classes.id')
                ->where('students.class_id', $classID)
                ->where('students.session', 'April 2024 to March 2025')
                ->where('students.status', 'active')
                ->when($tenantId, function ($q) use ($tenantId) {
                    $q->where('students.tenant_id', $tenantId);
                })

                ->where(function ($query) use ($searchValue) {
                    $query->where('students.studentName', 'like', '%' . $searchValue . '%')
                        ->orWhere('parents.parentName', 'like', '%' . $searchValue . '%')
                        ->orWhere('schools.schoolName', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.grno', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.gender', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.session', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.section', 'like', '%' . $searchValue . '%')
                        ->orWhere('classes.className', 'like', '%' . $searchValue . '%');
                })
                ->count();

            // Perform raw SQL query to retrieve paginated data
            $students = DB::table('students')
                ->join('parents', 'students.parent_id', '=', 'parents.id')
                ->join('schools', 'students.school_id', '=', 'schools.id')
                ->join('classes', 'students.class_id', '=', 'classes.id')
                // ->where('students.session', 'April 2023 to March 2024') 
                ->where('students.class_id', $classID)
                ->where('students.status', 'active')
                ->where('students.session', 'April 2024 to March 2025')
                ->when($tenantId, function ($q) use ($tenantId) {
                    $q->where('students.tenant_id', $tenantId);
                })
                ->select(
                    'students.id',
                    'students.session',
                    'students.status',
                    'schools.schoolName',
                    DB::raw("CONCAT(SUBSTRING_INDEX(students.grno, '-', 1), '-', classes.className) AS modifiedGrnoClassName"),
                    'students.gender',
                    'students.studentName',
                    'parents.parentName',
                    'parents.id as parent_id',
                    'classes.className',
                    'students.section'
                )
                ->where(function ($query) use ($searchValue) {
                    $query->where('students.studentName', 'like', '%' . $searchValue . '%')
                        ->orWhere('parents.parentName', 'like', '%' . $searchValue . '%')
                        ->orWhere('schools.schoolName', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.grno', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.gender', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.session', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.section', 'like', '%' . $searchValue . '%')
                        ->orWhere('classes.className', 'like', '%' . $searchValue . '%');
                })
                ->orderBy($sortBy, $sortDirection)
                ->offset($request->input('start')) // Offset based on start index
                ->limit($request->input('length')) // Limit based on length
                ->get(); // Use get() to retrieve the data
            // Data format for DataTables
            $data = [];
            $serialNumber = $request->input('start') + 1; // Initial serial number
            foreach ($students as $student) {

                $actionButton = '<div class="d-inline-flex align-items-center flex-nowrap actions" style="gap:4px;">'
                    . '<a href="' . route('students.view', $student->id) . '" class="btn btn-sm btn-secondary" title="View"><i class="bi bi-eye"></i></a>'
                    . '<a href="' . url('editStudent/' . $student->id) . '" class="btn btn-sm btn-primary" title="Edit"><i class="bi bi-pencil"></i></a>'
                    . '<a href="' . route('deleteStudent', ['studentID' => $student->id]) . '" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm(\'Delete this student?\')"><i class="bi bi-trash"></i></a>'
                    . '</div>';
                // Adding another button with a tag
                // $actionButton .= '<a href="promoteStudent/'.$student->id.'"><button type="button" class="btn btn-sm btn-success">Promote <i class="bi bi-arrow-right"></i> </button></a>';
                // $actionButton .= '<button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#promoteModal"  
                //           data-bs-id="'.$student->id.'" 
                //           data-bs-name="'.$student->studentName.'" 
                //           data-bs-class="'.$student->className.'" 
                //           data-bs-parent="'.$student->parentName.'"> 
                //           Promote  <i class="bi bi-arrow-right"></i>
                //           </button>&nbsp;';


                $rowData = [
                    'serialNumber' => $serialNumber++, // Incremental serial number
                    'schoolName' => $student->schoolName, // Include className from the 'students' table
                    'session' => $student->session, // Include session from the 'students' table
                    'className' => $student->className, // Include className from the 'students' table
                    'studentName' => $student->studentName,
                    'grno' => $student->modifiedGrnoClassName,
                    'gender' => $student->gender,
                    'parentName' => $student->parentName, // Include parentName from the 'parents' table
                    'parentId' => $student->parent_id,
                    'section' => $student->section,
                    'status' => $student->status,
                    'action' => $actionButton
                ];
                $data[] = $rowData;
            }
        } else {

            $columns = ['id', 'session', 'className', 'grno', 'status', 'gender', 'schoolName', 'studentName', 'parentName']; // Define columns to be selected
            $sortColumnIndex = $request->input('order.0.column');
            $sortDirection = $request->input('order.0.dir');
            $sortBy = $columns[$sortColumnIndex];

            // Searching
            $searchValue = $request->input('search.value');

            // Get total count of records
            $tenantId = auth()->user()->tenant_id ?? null;
            $totalData = DB::table('students')
                ->join('parents', 'students.parent_id', '=', 'parents.id')
                ->join('schools', 'students.school_id', '=', 'schools.id')
                ->join('classes', 'students.class_id', '=', 'classes.id')
                ->where('students.session', 'April 2024 to March 2025')
                ->where('students.status', 'active')
                ->when($tenantId, function ($q) use ($tenantId) {
                    $q->where('students.tenant_id', $tenantId);
                })
                ->where(function ($query) use ($searchValue) {
                    $query->where('students.studentName', 'like', '%' . $searchValue . '%')
                        ->orWhere('parents.parentName', 'like', '%' . $searchValue . '%')
                        ->orWhere('schools.schoolName', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.grno', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.gender', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.session', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.section', 'like', '%' . $searchValue . '%')
                        ->orWhere('classes.className', 'like', '%' . $searchValue . '%');
                })
                ->count();

            // Perform raw SQL query to retrieve paginated data
            $students = DB::table('students')
                ->join('parents', 'students.parent_id', '=', 'parents.id')
                ->join('schools', 'students.school_id', '=', 'schools.id')
                ->join('classes', 'students.class_id', '=', 'classes.id')
                ->where('students.session', 'April 2024 to March 2025')
                ->where('students.status', 'active')
                ->when($tenantId, function ($q) use ($tenantId) {
                    $q->where('students.tenant_id', $tenantId);
                })
                ->select(
                    'students.id',
                    'students.session',
                    'students.status',
                    'schools.schoolName',
                    DB::raw("CONCAT(SUBSTRING_INDEX(students.grno, '-', 1), '-', classes.className) AS modifiedGrnoClassName"),
                    'students.gender',
                    'students.studentName',
                    'parents.parentName',
                    'parents.id as parent_id',
                    'classes.className',
                    'students.section'
                )
                ->where(function ($query) use ($searchValue) {
                    $query->where('students.studentName', 'like', '%' . $searchValue . '%')
                        ->orWhere('parents.parentName', 'like', '%' . $searchValue . '%')
                        ->orWhere('schools.schoolName', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.grno', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.gender', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.session', 'like', '%' . $searchValue . '%')
                        ->orWhere('students.section', 'like', '%' . $searchValue . '%')
                        ->orWhere('classes.className', 'like', '%' . $searchValue . '%');
                })
                ->orderBy($sortBy, $sortDirection)
                ->offset($request->input('start')) // Offset based on start index
                ->limit($request->input('length')) // Limit based on length
                ->get(); // Use get() to retrieve the data
            // Data format for DataTables
            $data = [];
            $serialNumber = $request->input('start') + 1; // Initial serial number
            foreach ($students as $student) {

                $actionButton = '<div class="d-inline-flex align-items-center flex-nowrap actions" style="gap:4px;">'
                    . '<a href="' . route('students.view', $student->id) . '" class="btn btn-sm btn-secondary" title="View"><i class="bi bi-eye"></i></a>'
                    . '<a href="' . url('editStudent/' . $student->id) . '" class="btn btn-sm btn-primary" title="Edit"><i class="bi bi-pencil"></i></a>'
                    . '<a href="' . route('deleteStudent', ['studentID' => $student->id]) . '" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm(\'Delete this student?\')"><i class="bi bi-trash"></i></a>'
                    . '</div>';
                // Adding another button with a tag
                // $actionButton .= '<a href="promoteStudent/'.$student->id.'"><button type="button" class="btn btn-sm btn-success">Promote <i class="bi bi-arrow-right"></i> </button></a>';

                $rowData = [
                    'serialNumber' => $serialNumber++, // Incremental serial number
                    'schoolName' => $student->schoolName, // Include className from the 'students' table
                    'session' => $student->session, // Include session from the 'students' table
                    'className' => $student->className, // Include className from the 'students' table
                    'studentName' => $student->studentName,
                    'grno' => $student->modifiedGrnoClassName,
                    'gender' => $student->gender,
                    'parentName' => $student->parentName, // Include parentName from the 'parents' table
                    'section' => $student->section,
                    'status' => $student->status,
                    'action' => $actionButton
                ];
                $data[] = $rowData;
            }
        }



        // JSON response
        $response = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalData), // Count is the same for filtered data
            'data' => $data,
        ];

        return response()->json($response);
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
     * @param  \App\Models\students  $students
     * @return \Illuminate\Http\Response
     */
    public function show(students $students)
    {
        //
    }

    public function view($id)
    {
        // Build the same dataset as overview(), then render a Blade view
        $json = $this->overview($id);
        if (method_exists($json, 'getData')) {
            $payload = $json->getData(true);
        } else {
            // Fallback if not a response instance
            $payload = [];
        }
        if (!isset($payload['ok']) || !$payload['ok']) {
            abort(404, isset($payload['error']) ? $payload['error'] : 'Student not found');
        }
        return view('student_overview', [
            'profile' => $payload['profile'] ?? [],
            'results' => $payload['results'] ?? [],
            'behavior' => $payload['behavior'] ?? [],
            'subjects' => $payload['subjects'] ?? [],
            'fees' => $payload['fees'] ?? [],
            'online' => $payload['online_exams'] ?? [],
            'siblings' => $payload['siblings'] ?? [],
            'attendance' => $payload['attendance'] ?? [],
        ]);
    }

    public function overview($id)
    {
        try {
            $student = DB::table('students')
                ->leftJoin('parents', 'students.parent_id', '=', 'parents.id')
                ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
                ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
                ->where('students.id', (int)$id)
                ->select(
                    'students.*',
                    'parents.parentName as parentName',
                    'classes.className as className',
                    'schools.schoolName as schoolName'
                )
                ->first();
            if (!$student) {
                return response()->json(['ok' => false, 'error' => 'Student not found'], 404);
            }

            $grno = trim((string)$student->grno);
            $classId = (int)($student->class_id ?? 0);

            // Results (Manual Exams)
            $results = ['entries' => [], 'count' => 0];
            if (\Illuminate\Support\Facades\Schema::hasTable('manual_exam_entries') && $grno !== '') {
                $entries = DB::table('manual_exam_entries')
                    ->where('student_id', $grno)
                    ->orderByDesc('updated_at')
                    ->limit(10)
                    ->get();
                $results['entries'] = $entries;
                $results['count'] = DB::table('manual_exam_entries')->where('student_id', $grno)->count();
            }

            // Behavior attributes (assessment)
            $behavior = ['rows' => [], 'count' => 0];
            if (\Illuminate\Support\Facades\Schema::hasTable('student_behavior_attributes') && $grno !== '') {
                $rows = DB::table('student_behavior_attributes')
                    ->where('student_id', $grno)
                    ->orderByDesc('updated_at')->limit(10)->get();
                $behavior['rows'] = $rows;
                $behavior['count'] = DB::table('student_behavior_attributes')->where('student_id', $grno)->count();
            }

            // Subjects for class
            $subjects = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('subjects') && $classId) {
                $subjects = DB::table('subjects')
                    ->where('class_id', $classId)
                    ->where('status', 'active')->orderBy('sort_order')->orderBy('subject_name')->pluck('subject_name');
            }

            // Fees/Challans (best-effort)
            $fees = ['count' => 0, 'latest' => null];
            if (\Illuminate\Support\Facades\Schema::hasTable('challans')) {
                $q = DB::table('challans');
                if (\Illuminate\Support\Facades\Schema::hasColumn('challans', 'student_id')) {
                    $q->where('student_id', (int)$id);
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn('challans', 'grno')) {
                    $q->where('grno', $grno);
                }
                $fees['count'] = $q->count();
                $fees['latest'] = $q->orderByDesc('updated_at')->first();
            }

            // Exams (online) best-effort
            $online = ['count' => 0];
            if (\Illuminate\Support\Facades\Schema::hasTable('student_exams')) {
                $qe = DB::table('student_exams');
                if (\Illuminate\Support\Facades\Schema::hasColumn('student_exams', 'student_id')) {
                    $qe->where('student_id', (int)$id);
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn('student_exams', 'grno')) {
                    $qe->where('grno', $grno);
                }
                $online['count'] = $qe->count();
            }

            // Siblings (students with same parent_id)
            $siblings = [];
            if (isset($student->parent_id) && $student->parent_id) {
                $siblings = DB::table('students')
                    ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
                    ->where('students.parent_id', $student->parent_id)
                    ->where('students.id', '!=', (int)$id)
                    ->select('students.id', 'students.studentName as name', 'students.grno', 'classes.className as class')
                    ->orderBy('students.studentName')
                    ->get();
            }

            // Attendance summary
            $attendance = ['totals' => [], 'recent' => []];
            if (\Illuminate\Support\Facades\Schema::hasTable('attendances')) {
                // yearly totals by status
                $totals = DB::table('attendances')
                    ->where('student_id', (int)$id)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')->pluck('count', 'status');
                $attendance['totals'] = $totals;
                // last 30 days
                $recent = DB::table('attendances')
                    ->where('student_id', (int)$id)
                    ->where('date', '>=', now()->subDays(30)->toDateString())
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')->pluck('count', 'status');
                $attendance['recent'] = $recent;
            }

            // DOB and age (best-effort)
            $dobVal = null;
            $ageYears = null;
            if (isset($student->dob) && $student->dob) {
                $dobVal = $student->dob;
            } elseif (isset($student->date_of_birth) && $student->date_of_birth) {
                $dobVal = $student->date_of_birth;
            }
            if ($dobVal) {
                try {
                    $ageYears = \Carbon\Carbon::parse($dobVal)->age;
                } catch (\Exception $e) {
                    $ageYears = null;
                }
            }

            // Refine fees paid/pending counts
            if (\Illuminate\Support\Facades\Schema::hasTable('challans')) {
                $qBase = DB::table('challans');
                if (\Illuminate\Support\Facades\Schema::hasColumn('challans', 'student_id')) {
                    $qBase->where('student_id', (int)$id);
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn('challans', 'grno')) {
                    $qBase->where('grno', $grno);
                }
                $fees['count'] = (clone $qBase)->count();
                $fees['latest'] = (clone $qBase)->orderByDesc('updated_at')->first();
                $fees['paid'] = 0;
                $fees['pending'] = 0;
                if (\Illuminate\Support\Facades\Schema::hasColumn('challans', 'status')) {
                    $fees['paid'] = (clone $qBase)->whereIn('status', ['paid', 'Paid', 'PAID'])->count();
                    $fees['pending'] = max(0, $fees['count'] - $fees['paid']);
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn('challans', 'is_paid')) {
                    $fees['paid'] = (clone $qBase)->where('is_paid', 1)->count();
                    $fees['pending'] = (clone $qBase)->where('is_paid', 0)->count();
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn('challans', 'paid_amount') && \Illuminate\Support\Facades\Schema::hasColumn('challans', 'total_amount')) {
                    $fees['paid'] = (clone $qBase)->whereRaw('paid_amount >= total_amount')->count();
                    $fees['pending'] = (clone $qBase)->whereRaw('paid_amount < total_amount')->count();
                }
            }

            return response()->json([
                'ok' => true,
                'profile' => [
                    'id' => $student->id,
                    'name' => $student->studentName,
                    'grno' => $grno,
                    'gender' => $student->gender,
                    'class' => $student->className,
                    'section' => $student->section ?? '',
                    'school' => $student->schoolName,
                    'parent' => $student->parentName,
                    'session' => $student->session,
                    'status' => $student->status,
                    'dob' => $dobVal,
                    'age' => $ageYears,
                ],
                'results' => $results,
                'behavior' => $behavior,
                'subjects' => $subjects,
                'fees' => $fees,
                'online_exams' => $online,
                'siblings' => $siblings,
                'attendance' => $attendance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\students  $students
     * @return \Illuminate\Http\Response
     */
    public function edit(students $students)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\students  $students
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, students $students)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\students  $students
     * @return \Illuminate\Http\Response
     */
    public function destroy(students $students)
    {
        //
    }

    public function promoteStudent(Request $request)
    {

        // echo $studentId;
        $selectedValues = json_decode($request->promoteToClass);
        $NewClassID = $selectedValues->id;
        $NewClassName = $selectedValues->className;
        //print_r($NewClassName);
        // die();
        $tenantId = auth()->user()->tenant_id ?? null;
        $studentDetail = DB::table('students')->when($tenantId, function ($q) use ($tenantId) {
            $q->where('students.tenant_id', $tenantId);
        })->where('id', '=', $request->id)->first();

        // dd($studentDetail);
        // die();
        $affected = DB::table('students')
            ->where('id', $request->id)
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('students.tenant_id', $tenantId);
            })
            ->update(['status' => 'promoted-to-' . $NewClassName]);

        DB::table('students')->insert([
            'session' => 'April 2024 to March 2025',
            'class_id' => $NewClassID,
            'school_id' => $studentDetail->school_id,
            'parent_id' => $studentDetail->parent_id,
            'grno' => $studentDetail->grno,
            'gender' => $studentDetail->gender,
            'status' => 'active',
            'studentName' => $studentDetail->studentName,
            'tenant_id' => $tenantId
        ]);

        return redirect()->back();

        dd($studentDetail);
    }

    public function studentsListSLC()
    {
        $tenantId = auth()->user()->tenant_id ?? null;

        // Query students based on 'SLC' status or having a certificate fee in challans
        $studentListSLC = DB::table('students')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('challans', function($join) {
                $join->on('students.grno', '=', 'challans.grno')
                     ->where('challans.clc', '>', 0);
            })
            ->where(function($query) {
                $query->where('students.status', 'SLC')
                      ->orWhere('challans.clc', '>', 0);
            })
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('students.tenant_id', $tenantId);
            })
            ->select(
                'students.grno',
                'classes.className as class_name',
                'students.studentName as student_id', // View uses student_id for name
                'challans.month',
                'challans.year',
                'challans.clc',
                'students.status'
            )
            ->get();

        return view('studentListSLC', Compact('studentListSLC'));
    }
}
