<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\TeacherAttendance;
use App\Models\Classes;
use App\Models\students;
use App\Models\teachers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display student attendance form
     */
    public function students(Request $request)
    {
        // Get current date if not provided
        $date = $request->get('date', date('Y-m-d'));
        
        // Get current session if not provided
        $d = \Carbon\Carbon::parse($date);
        $y = $d->year;
        $m = $d->month;
        $fallbackSession = ($m < 8) ? (($y - 1) . '-' . $y) : ($y . '-' . ($y + 1));
        $sessionValue = $request->get('session', $fallbackSession);
        
        // Get selected class ID
        $selectedClassId = $request->get('class_id');

        // Current tenant
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        
        // Get all active classes (TenantOwned scope applies)
        $classes = Classes::active()->get();
        
        // Load sessions dropdown (academic years) with optional tenant scoping and fallbacks
        $sessions = collect();
        if (Schema::hasTable('academicyears')) {
            $q = DB::table('academicyears')
                ->select('academicYear', 'is_active')
                ->orderBy('academicYear');
            if ($tenantId && Schema::hasColumn('academicyears', 'tenant_id')) {
                $q->where('tenant_id', $tenantId);
            }
            $sessions = $q->get();
        } elseif (Schema::hasTable('academic_years')) {
            $q = DB::table('academic_years')
                ->select(DB::raw('label as academicYear'), 'is_active')
                ->orderBy('label');
            if ($tenantId && Schema::hasColumn('academic_years', 'tenant_id')) {
                $q->where('tenant_id', $tenantId);
            }
            $sessions = $q->get();
        } else {
            $sessionValues = collect();
            if (Schema::hasTable('attendances')) {
                $qa = DB::table('attendances')->select('session')->distinct();
                if ($tenantId && Schema::hasColumn('attendances', 'tenant_id')) {
                    $qa->where('tenant_id', $tenantId);
                }
                $sessionValues = $sessionValues->merge($qa->pluck('session'));
            }
            if (Schema::hasTable('students')) {
                $qs = DB::table('students')->select('session')->distinct();
                if ($tenantId && Schema::hasColumn('students', 'tenant_id')) {
                    $qs->where('tenant_id', $tenantId);
                }
                $sessionValues = $sessionValues->merge($qs->pluck('session'));
            }
            if (Schema::hasTable('classes')) {
                $qc = DB::table('classes')->select('session')->distinct();
                if ($tenantId && Schema::hasColumn('classes', 'tenant_id')) {
                    $qc->where('tenant_id', $tenantId);
                }
                $sessionValues = $sessionValues->merge($qc->pluck('session'));
            }
            $sessionValues = $sessionValues->filter()->unique()->sort();
            $sessions = $sessionValues->map(function ($s) {
                return (object) ['academicYear' => $s, 'is_active' => null];
            });
        }

        // If no explicit session provided, prefer active session or first available
        if (!$request->has('session')) {
            $active = $sessions->first(function ($s) {
                return ($s->is_active ?? null) === 'yes' || ($s->is_active ?? null) === 1;
            });
            if ($active) {
                $sessionValue = $active->academicYear;
            } elseif ($sessions->count() > 0) {
                $sessionValue = $sessions->first()->academicYear;
            }
        }

        // Resolve selected class name for display
        $selectedClass = null;
        if ($selectedClassId) {
            $selectedClass = Classes::find($selectedClassId);
        }

        // Initialize students array
        $students = collect();
        
        // Initialize existing attendance records
        $existingByStudent = [];
        
        // If a class is selected, get students and existing attendance
        if ($selectedClassId) {
            $students = students::where('class_id', $selectedClassId)
                               ->where('status', 'active')
                               ->orderBy('studentName')
                               ->get();
            
            // Get existing attendance records for this date, class, and session
            $existingRecords = Attendance::where('class_id', $selectedClassId)
                                       ->where('date', $date)
                                       ->where('session', $sessionValue)
                                       ->get()
                                       ->keyBy('student_id');
            
            $existingByStudent = $existingRecords->toArray();
        }
        
        return view('attendance_students', compact(
            'classes', 
            'students', 
            'date', 
            'sessionValue', 
            'selectedClassId', 
            'existingByStudent',
            'sessions',
            'selectedClass'
        ));
    }
    
    /**
     * Store student attendance
     */
    public function storeStudents(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'session' => 'required|string',
            'entries' => 'required|array',
            'entries.*.status' => 'required|in:present,absent,leave,late',
            'entries.*.remarks' => 'nullable|string|max:255'
        ]);
        
        try {
            DB::beginTransaction();
            
            $classId = $request->class_id;
            $date = $request->date;
            $session = $request->session;
            $entries = $request->entries;
            
            // Process each student's attendance
            foreach ($entries as $studentId => $data) {
                // Update or create attendance record
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class_id' => $classId,
                        'date' => $date,
                        'session' => $session
                    ],
                    [
                        'status' => $data['status'],
                        'remarks' => $data['remarks'] ?? null,
                        'school_id' => Auth::user()->school_id ?? null,
                        'teacher_id' => Auth::id(),
                        'created_by' => Auth::id()
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->back()->with('message', 'Attendance saved successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->with('error', 'Failed to save attendance. Please try again.')
                           ->withInput();
        }
    }
    
    /**
     * Display teacher attendance form
     */
    public function teachers(Request $request)
    {
        // Get current date if not provided
        $date = $request->get('date', date('Y-m-d'));
        
        // Get all active teachers
        $teachers = teachers::where('status', 'active')
                           ->orderBy('teacherName')
                           ->get();
        
        // Get existing attendance records for this date
        $existingRecords = TeacherAttendance::where('date', $date)
                                          ->get()
                                          ->keyBy('teacher_id');
        
        $existingByTeacher = $existingRecords->toArray();
        
        return view('attendance_teachers', compact(
            'teachers', 
            'date', 
            'existingByTeacher'
        ));
    }
    
    /**
     * Store teacher attendance
     */
    public function storeTeachers(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'entries' => 'required|array',
            'entries.*.status' => 'required|in:present,absent,leave,late',
            'entries.*.remarks' => 'nullable|string|max:255'
        ]);
        
        try {
            DB::beginTransaction();
            
            $date = $request->date;
            $entries = $request->entries;
            
            // Process each teacher's attendance
            foreach ($entries as $teacherId => $data) {
                // Update or create attendance record
                TeacherAttendance::updateOrCreate(
                    [
                        'teacher_id' => $teacherId,
                        'date' => $date
                    ],
                    [
                        'status' => $data['status'],
                        'remarks' => $data['remarks'] ?? null,
                        'school_id' => Auth::user()->school_id ?? null,
                        'marked_by' => Auth::id()
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->back()->with('message', 'Teacher attendance saved successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->with('error', 'Failed to save teacher attendance. Please try again.')
                           ->withInput();
        }
    }
    
    /**
     * Display student attendance monthly report
     */
    public function studentReports(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        $selectedClassId = $request->get('class_id');
        
        // Get all active classes
        $classes = Classes::active()->get();
        
        // Initialize variables
        $students = collect();
        $attendance = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        // If a class is selected, get data
        if ($selectedClassId) {
            $students = students::where('class_id', $selectedClassId)
                               ->where('status', 'active')
                               ->orderBy('studentName')
                               ->get();
            
            // Get attendance records for the month
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);
            
            $records = Attendance::where('class_id', $selectedClassId)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->get();
            
            // Organize attendance data
            foreach ($records as $record) {
                $day = $record->date->day;
                $attendance[$record->student_id][$day] = $record->status;
            }
        }
        
        return view('attendance_report_students', compact(
            'classes',
            'students', 
            'month', 
            'year', 
            'selectedClassId', 
            'attendance',
            'daysInMonth'
        ));
    }
    
    /**
     * Display teacher attendance monthly report
     */
    public function teacherReports(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        
        // Get all active teachers
        $teachers = teachers::where('status', 'active')
                           ->orderBy('teacherName')
                           ->get();
        
        $attendance = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        // Get attendance records for the month
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);
        
        $records = TeacherAttendance::whereBetween('date', [$startDate, $endDate])->get();
        
        // Organize attendance data
        foreach ($records as $record) {
            $day = $record->date->day;
            $attendance[$record->teacher_id][$day] = $record->status;
        }
        
        return view('attendance_report_teachers', compact(
            'teachers', 
            'month', 
            'year', 
            'attendance',
            'daysInMonth'
        ));
    }
    
    /**
     * Display student attendance yearly report
     */
    public function studentReportsYearly(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $selectedClassId = $request->get('class_id');
        
        // Get all active classes
        $classes = Classes::active()->get();
        
        // Initialize variables
        $students = collect();
        $totals = [];
        
        // If a class is selected, get data
        if ($selectedClassId) {
            $students = students::where('class_id', $selectedClassId)
                               ->where('status', 'active')
                               ->orderBy('studentName')
                               ->get();
            
            // Get attendance records for the year
            $startDate = $year . '-01-01';
            $endDate = $year . '-12-31';
            
            $records = Attendance::where('class_id', $selectedClassId)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->selectRaw('student_id, status, COUNT(*) as count')
                                ->groupBy('student_id', 'status')
                                ->get();
            
            // Organize totals
            foreach ($records as $record) {
                $totals[$record->student_id][$record->status] = $record->count;
            }
        }
        
        return view('attendance_report_students_yearly', compact(
            'classes',
            'students', 
            'year', 
            'selectedClassId', 
            'totals'
        ));
    }
    
    /**
     * Display teacher attendance yearly report
     */
    public function teacherReportsYearly(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // Get all active teachers
        $teachers = teachers::where('status', 'active')
                           ->orderBy('teacherName')
                           ->get();
        
        $totals = [];
        
        // Get attendance records for the year
        $startDate = $year . '-01-01';
        $endDate = $year . '-12-31';
        
        $records = TeacherAttendance::whereBetween('date', [$startDate, $endDate])
                                  ->selectRaw('teacher_id, status, COUNT(*) as count')
                                  ->groupBy('teacher_id', 'status')
                                  ->get();
        
        // Organize totals
        foreach ($records as $record) {
            $totals[$record->teacher_id][$record->status] = $record->count;
        }
        
        return view('attendance_report_teachers_yearly', compact(
            'teachers', 
            'year', 
            'totals'
        ));
    }
}
