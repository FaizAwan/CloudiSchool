<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;
use Hash;
use Auth;
use Carbon\Carbon;
use App\Libraries\WhatsAppSender;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        // Log all POST requests for debugging
        if (request()->isMethod('POST')) {
            \Log::info('POST request to HomeController', [
                'uri' => request()->getUri(),
                'method' => request()->getMethod(),
                'data' => request()->all()
            ]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // WhatsApp notification - Disabled for performance as it blocks the main thread.
        // If needed, this should be dispatched as a background job or called via AJAX from the UI.
        /*
         if (!session()->has('message_sent')) {
         $email = Auth::user()->name;
         $recipientNumber = '923332775756';
         WhatsAppSender::sendMessage($recipientNumber, $email);
         session(['message_sent' => true]);
         }
         */

        $user = Auth::user();
        $tenantId = $user->tenant_id ?? null;
        $role = $user->role;

        // Use caching to make the dashboard load in milliseconds
        // Cache for 5 minutes (300 seconds)
        $cacheKey = "dashboard_stats_" . ($tenantId ?? 'global') . "_" . $role . "_" . $user->id;

        $dashboardData = \Cache::remember($cacheKey, 300, function () use ($tenantId, $user) {
            // Get current session from active academic year
            $academicTable = Schema::hasTable('academicyears') ? 'academicyears' : (Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
            $activeAcademicYear = DB::table($academicTable)->where('is_active', 'yes')->first();

            $year = date('Y');
            $month = date('n');
            $fallbackSession = ($month < 8) ? (($year - 1) . '-' . $year) : ($year . '-' . ($year + 1));

            $sessionLabel = ($academicTable === 'academic_years') ? 'label' : 'academicYear';
            $currentSession = $activeAcademicYear ? ($activeAcademicYear->$sessionLabel ?? $activeAcademicYear->academicYear ?? $activeAcademicYear->label) : $fallbackSession;

            $teacher = null;
            if ($user->role == 'teacher') {
                $teacher = DB::table('teachers')
                    ->when($tenantId, function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                }
                )
                    ->where('email', $user->email)
                    ->first();
            }

            $feesStats = $this->getFeesStatistics($currentSession, $tenantId, $teacher);
            $basicStats = $this->getBasicStatistics($tenantId, $user->role, $teacher);

            return array_merge((array)$feesStats, (array)$basicStats);
        });

        return view('home', compact('dashboardData'));
    }

    /**
     * Get comprehensive fees statistics
     */
    private function getFeesStatistics($currentSession, $tenantId = null, $teacher = null)
    {
        try {
            // Build base query conditions for challans
            $sessionCondition = ['challans.session' => $currentSession];
            $classCondition = $teacher ? ['challans.class_name' => $teacher->className] : [];
            $baseWhere = array_merge((array)$sessionCondition, (array)$classCondition);

            // Total fees collected from challans
            $totalFeesCollected = DB::table('challans')
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
                ->where($baseWhere)
                ->where('challans.status', 'paid')
                ->sum('challans.total_fee');

            // Pending fees from challans
            $pendingFees = DB::table('challans')
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
                ->where($baseWhere)
                ->where('challans.status', 'un-paid')
                ->sum('challans.total_fee');

            // Add fee structure totals from fees table
            $totalFeeStructure = DB::table('fees')
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
                ->where('session', $currentSession)
                ->when($teacher, function ($query) use ($teacher) {
                return $query->where('class_name', $teacher->className);
            })
                ->sum('fee_value');

            // Total students with fees
            $totalStudentsWithFees = DB::table('challans')
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
                ->where($baseWhere)
                ->distinct('challans.student_id')
                ->count('challans.student_id');

            // Students with pending fees
            $studentsWithPendingFees = DB::table('challans')
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
                ->where($baseWhere)
                ->where('challans.status', 'un-paid')
                ->distinct('challans.student_id')
                ->count('challans.student_id');

            // Monthly fee collection trends (last 6 months)
            $monthlyFeeTrends = DB::table('challans')
                ->select(DB::raw('MONTH(challans.created_at) as month, YEAR(challans.created_at) as year, SUM(challans.total_fee) as total'))
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
                ->where($baseWhere)
                ->where('challans.status', 'paid')
                ->where('challans.created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy(DB::raw('YEAR(challans.created_at), MONTH(challans.created_at)'))
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            // Class-wise fee distribution
            $classWiseFees = DB::table('challans')
                ->select('challans.class_name', DB::raw('SUM(challans.total_fee) as total_collected, COUNT(*) as total_challans'))
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
                ->where('challans.session', $currentSession)
                ->where('challans.status', 'paid')
                ->groupBy('challans.class_name')
                ->get();

            // Fee payment status breakdown
            $paymentStatusBreakdown = DB::table('challans')
                ->select('challans.status', DB::raw('COUNT(*) as count, SUM(challans.total_fee) as amount'))
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
                ->where($baseWhere)
                ->groupBy('challans.status')
                ->get();

            // Recent fee payments (last 10)
            $recentFeePayments = DB::table('challans')
                ->join('students', 'challans.grno', '=', 'students.grno')
                ->select('challans.*', 'students.studentName')
                ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('challans.tenant_id', $tenantId)->where('students.tenant_id', $tenantId);
            })
                ->where('challans.session', $currentSession)
                ->when($teacher, function ($query) use ($teacher) {
                return $query->where('challans.class_name', $teacher->className);
            })
                ->where('challans.status', 'paid')
                ->orderBy('challans.updated_at', 'desc')
                ->limit(10)
                ->get();

            return [
                'totalFeesCollected' => $totalFeesCollected ?: 0,
                'pendingFees' => $pendingFees ?: 0,
                'totalStudentsWithFees' => $totalStudentsWithFees ?: 0,
                'studentsWithPendingFees' => $studentsWithPendingFees ?: 0,
                'monthlyFeeTrends' => $monthlyFeeTrends,
                'classWiseFees' => $classWiseFees,
                'paymentStatusBreakdown' => $paymentStatusBreakdown,
                'recentFeePayments' => $recentFeePayments,
                'feesCollectionRate' => $totalStudentsWithFees > 0 ? 
                round((($totalStudentsWithFees - $studentsWithPendingFees) / $totalStudentsWithFees) * 100, 1) : 0
            ];
        }
        catch (\Exception $e) {
            // Return default values if there are any database errors
            return [
                'totalFeesCollected' => 0,
                'pendingFees' => 0,
                'totalStudentsWithFees' => 0,
                'studentsWithPendingFees' => 0,
                'monthlyFeeTrends' => collect([]),
                'classWiseFees' => collect([]),
                'paymentStatusBreakdown' => collect([]),
                'recentFeePayments' => collect([]),
                'feesCollectionRate' => 0
            ];
        }
    }

    /**
     * Get comprehensive exam statistics
     */
    private function getExamStatistics($currentSession, $teacher = null)
    {
        // Check if exam tables exist
        try {
            // Build base query conditions for exams
            $examSessionCondition = ['exams.session' => $currentSession];
            $examClassCondition = $teacher ? ['exams.class_name' => $teacher->className] : [];
            $examBaseWhere = array_merge((array)$examSessionCondition, (array)$examClassCondition);

            // Total exams
            $totalExams = DB::table('exams')
                ->where($examBaseWhere)
                ->count();

            // Active exams (published status)
            $activeExams = DB::table('exams')
                ->where($examBaseWhere)
                ->where('exams.status', 'published')
                ->count();

            // Total exam attempts
            $totalAttempts = DB::table('student_exam_attempts')
                ->join('exams', 'student_exam_attempts.exam_id', '=', 'exams.id')
                ->where('exams.session', $currentSession)
                ->when($teacher, function ($query) use ($teacher) {
                return $query->where('exams.class_name', $teacher->className);
            })
                ->count();

            // Average performance
            $avgPerformance = DB::table('student_exam_attempts')
                ->join('exams', 'student_exam_attempts.exam_id', '=', 'exams.id')
                ->where('exams.session', $currentSession)
                ->when($teacher, function ($query) use ($teacher) {
                return $query->where('exams.class_name', $teacher->className);
            })
                ->avg('student_exam_attempts.percentage') ?: 0;

            // Subject-wise performance
            $subjectWisePerformance = DB::table('student_exam_attempts')
                ->join('exams', 'student_exam_attempts.exam_id', '=', 'exams.id')
                ->join('subjects', 'exams.subject_id', '=', 'subjects.id')
                ->select('subjects.subject_name', DB::raw('AVG(student_exam_attempts.percentage) as avg_percentage'))
                ->where('exams.session', $currentSession)
                ->when($teacher, function ($query) use ($teacher) {
                return $query->where('exams.class_name', $teacher->className);
            })
                ->groupBy('subjects.subject_name')
                ->get();

            // Grade distribution
            $gradeDistribution = DB::table('student_exam_attempts')
                ->join('exams', 'student_exam_attempts.exam_id', '=', 'exams.id')
                ->select('student_exam_attempts.grade', DB::raw('COUNT(*) as count'))
                ->where('exams.session', $currentSession)
                ->when($teacher, function ($query) use ($teacher) {
                return $query->where('exams.class_name', $teacher->className);
            })
                ->whereNotNull('student_exam_attempts.grade')
                ->groupBy('student_exam_attempts.grade')
                ->get();

            // Recent exam results (last 10)
            $recentExamResults = DB::table('student_exam_attempts')
                ->join('exams', 'student_exam_attempts.exam_id', '=', 'exams.id')
                ->join('students', 'student_exam_attempts.student_id', '=', 'students.id')
                ->select('exams.exam_name', 'students.studentName', 'student_exam_attempts.*')
                ->where('exams.session', $currentSession)
                ->when($teacher, function ($query) use ($teacher) {
                return $query->where('exams.class_name', $teacher->className);
            })
                ->orderBy('student_exam_attempts.end_time', 'desc')
                ->limit(10)
                ->get();

            // Exam completion rates
            $examCompletionRate = $totalExams > 0 ? 
                DB::table('exams')
                ->where($examBaseWhere)
                ->where('exams.status', 'completed')
                ->count() / $totalExams * 100 : 0;

            // Monthly exam trends (last 6 months)
            $monthlyExamTrends = DB::table('exams')
                ->select(DB::raw('MONTH(exams.exam_date) as month, YEAR(exams.exam_date) as year, COUNT(*) as total'))
                ->where($examBaseWhere)
                ->where('exams.exam_date', '>=', Carbon::now()->subMonths(6))
                ->groupBy(DB::raw('YEAR(exams.exam_date), MONTH(exams.exam_date)'))
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            return [
                'totalExams' => $totalExams,
                'activeExams' => $activeExams,
                'totalAttempts' => $totalAttempts,
                'avgPerformance' => round($avgPerformance, 1),
                'subjectWisePerformance' => $subjectWisePerformance,
                'gradeDistribution' => $gradeDistribution,
                'recentExamResults' => $recentExamResults,
                'examCompletionRate' => round($examCompletionRate, 1),
                'monthlyExamTrends' => $monthlyExamTrends
            ];
        }
        catch (\Exception $e) {
            // Return default values if exam tables don't exist
            return [
                'totalExams' => 0,
                'activeExams' => 0,
                'totalAttempts' => 0,
                'avgPerformance' => 0,
                'subjectWisePerformance' => collect([]),
                'gradeDistribution' => collect([]),
                'recentExamResults' => collect([]),
                'examCompletionRate' => 0,
                'monthlyExamTrends' => collect([])
            ];
        }
    }

    /**
     * Get basic statistics for classes, teachers, students, schools
     */
    private function getBasicStatistics($tenantId = null, $role = 'admin', $teacher = null)
    {
        try {
            // For non-superadmin users, scope by tenant_id
            $scope = function ($q) use ($tenantId, $role) {
                if ($role !== 'superadmin' && $tenantId) {
                    $q->where('tenant_id', $tenantId);
                }
                return $q;
            };

            // Total classes
            $totalClasses = $scope(DB::table('classes'))->count();

            // Total teachers
            $totalTeachers = $scope(DB::table('teachers'))->count();

            // Active teachers (status = 'active')
            if (Schema::hasColumn('teachers', 'status')) {
                $activeTeachers = $scope(DB::table('teachers'))
                    ->where('status', 'active')
                    ->count();
            }
            else {
                // If no status column, assume all are active
                $activeTeachers = $totalTeachers;
            }

            // Total students
            $totalStudents = $scope(DB::table('students'))->count();

            // Active students
            if (Schema::hasColumn('students', 'status')) {
                $activeStudents = $scope(DB::table('students'))
                    ->where('status', 'active')
                    ->count();
            }
            else {
                $activeStudents = $totalStudents;
            }

            if ($activeStudents == 0 && $totalStudents > 0) {
            // Fallback if status exists but no students are marked active (possibly data migration issue)
            // However, strictly speaking we should trust the status if the column exists.
            // But let's keep the original fallback logic just in benefit of doubt, 
            // though usually we should trust the query.
            // $activeStudents = $totalStudents; 
            }

            // Total students in classes (students who are enrolled)
            $studentsInClasses = $scope(DB::table('students'))
                ->whereNotNull('class_id')
                ->count();

            // Total schools
            $totalSchools = ($role === 'superadmin')
                ?DB::table('schools')->count()
                : 1; // tenant sees their own school only

            return [
                'totalClasses' => $totalClasses,
                'totalTeachers' => $totalTeachers,
                'activeTeachers' => $activeTeachers,
                'totalStudents' => $totalStudents,
                'activeStudents' => $activeStudents,
                'studentsInClasses' => $studentsInClasses,
                'totalSchools' => $totalSchools,
                'schoolsActive' => $totalSchools,
            ];
        }
        catch (\Exception $e) {
            \Log::error('Error in getBasicStatistics: ' . $e->getMessage());
            // Return default values if there are any database errors
            return [
                'totalClasses' => 0,
                'totalTeachers' => 0,
                'activeTeachers' => 0,
                'totalStudents' => 0,
                'activeStudents' => 0,
                'studentsInClasses' => 0,
                'totalSchools' => 1,
                'schoolsActive' => 1,
            ];
        }
    }

    public function weeklyTimetable()
    {
        try {
            $this->ensurePeriodsTable();
        }
        catch (\Throwable $e) {
        // ignore
        }

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        try {
            $teachers = DB::table('teachers')
                ->when($tenantId, fn($q) => $q->where('teachers.tenant_id', $tenantId))
                ->orderBy('teacherName', 'ASC')
                ->get();
        }
        catch (\Throwable $e) {
            $teachers = collect([]);
        }

        try {
            $periodsQuery = DB::table('periods');
            if ($tenantId && Schema::hasColumn('periods', 'tenant_id')) {
                $periodsQuery->where('tenant_id', $tenantId);
            }
            $periods = $periodsQuery->get();
        }
        catch (\Throwable $e) {
            $periods = collect([]);
        }

        try {
            $classes = DB::table('classes')
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->get();
        }
        catch (\Throwable $e) {
            $classes = collect([]);
        }

        try {
            $subjects = DB::table('subjects')
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->orderBy('subject_name', 'ASC')
                ->get();
        }
        catch (\Throwable $e) {
            $subjects = collect([]);
        }

        // Pre-fetch all timetables to avoid N+1 queries in view
        $timetables = DB::table('timetables')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();

        return view('timetable', Compact('classes', 'teachers', 'periods', 'subjects', 'timetables'));
    }

    public function weeklyTimetableByClass()
    {
        try {
            $this->ensurePeriodsTable();
        }
        catch (\Throwable $e) {
        }
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        try {
            $classes = DB::table('classes')
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->orderBy('className', 'ASC')
                ->get();
        }
        catch (\Throwable $e) {
            $classes = collect([]);
        }
        try {
            $periodsQuery = DB::table('periods');
            if ($tenantId && Schema::hasColumn('periods', 'tenant_id')) {
                $periodsQuery->where('tenant_id', $tenantId);
            }
            $periods = $periodsQuery->get();
        }
        catch (\Throwable $e) {
            $periods = collect([]);
        }
        return view('timetable_classes', Compact('classes', 'periods', 'days'));
    }

    public function weeklyTimetableBySubject()
    {
        try {
            $this->ensurePeriodsTable();
        }
        catch (\Throwable $e) {
        }
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        try {
            $subjects = DB::table('subjects')
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->orderBy('subject_name', 'ASC')
                ->get();
        }
        catch (\Throwable $e) {
            $subjects = collect([]);
        }
        try {
            $periodsQuery = DB::table('periods');
            if ($tenantId && Schema::hasColumn('periods', 'tenant_id')) {
                $periodsQuery->where('tenant_id', $tenantId);
            }
            $periods = $periodsQuery->get();
        }
        catch (\Throwable $e) {
            $periods = collect([]);
        }
        return view('timetable_subjects', Compact('subjects', 'periods', 'days'));
    }

    public function addTimetable(Request $request)
    {

        // Validate the request
        $request->validate([
            'teacher_id' => 'required|integer',
            'period_id' => 'required|integer',
            'day' => 'required|string',
            'subject' => 'required|string',
            'class_id' => 'required|string',
        ]);

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        // Check if this teacher-day-period combination already exists (scoped)
        $existingEntry = DB::table('timetables')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('teacher_id', $request->teacher_id)
            ->where('day', $request->day)
            ->where('period_id', $request->period_id)
            ->first();

        if ($existingEntry) {
            return redirect()->back()->with(
                'errorMessage',
                'This teacher is already assigned to this period on ' . $request->day . '. '
                . 'Current assignment: ' . $existingEntry->subject . ' for ' . $existingEntry->class
            );
        }

        try {
            $timeTableID = DB::table('timetables')->insertGetId([
                'tenant_id' => $tenantId,
                'teacher_id' => $request->teacher_id,
                'period_id' => $request->period_id,
                'day' => $request->day,
                'subject' => $request->subject,
                'class' => $request->class_id,
            ]);

            return redirect()->back()->with('message', 'Timetable Added for Specific Class');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('errorMessage', 'Error adding timetable entry: ' . $e->getMessage());
        }
    }

    public function periods()
    {
        $this->ensurePeriodsTable();
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;
        $schoolId = $user->school_id ?? null;

        // Get only the school record that matches the user's specific context for the DROPDOWN
        $schoolList = DB::table('schools')
            ->when($tenantId, fn($q) => $q->where('id', $tenantId))
            ->when(!$tenantId && $schoolId, fn($q) => $q->where('id', $schoolId))
            ->get();

        if ($schoolList->isEmpty() && $tenantId) {
            $schoolList = DB::table('schools')->where('tenant_id', $tenantId)->limit(1)->get();
        }

        // Get periods for the authorized schools only
        $query = DB::table('periods')
            ->leftJoin('schools', 'periods.school_id', '=', 'schools.id')
            ->select('periods.*', 'schools.schoolName as school_name');

        if ($tenantId) {
            $query->where('periods.tenant_id', $tenantId);
        }
        elseif ($schoolId) {
            $query->where('periods.school_id', $schoolId);
        }

        // Filter list to only show data for the specific branch(es) authorized in schoolList
        $authorizedSchoolIds = $schoolList->pluck('id')->toArray();
        if (!empty($authorizedSchoolIds)) {
            $query->whereIn('periods.school_id', $authorizedSchoolIds);
        }

        $periods = $query->orderBy('periods.periodName', 'asc')->get();

        // Additional data for Timetable Visibility
        $teachers = DB::table('teachers')
            ->when($tenantId, fn($q) => $q->where('teachers.tenant_id', $tenantId))
            ->orderBy('teacherName', 'ASC')
            ->get();

        $classes = DB::table('classes')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();

        $subjects = DB::table('subjects')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('subject_name', 'ASC')
            ->get();

        // Pre-fetch all timetables to avoid N+1 queries in view
        $timetables = DB::table('timetables')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();

        return view('periods', Compact('periods', 'schoolList', 'teachers', 'classes', 'subjects', 'timetables'));
    }


    public function getPeriods(Request $request)
    {
        $this->ensurePeriodsTable();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $columns = ['id', 'periodName', 'day', 'start_time', 'end_time'];
        $sortColumnIndex = $request->input('order.0.column', 0);
        $sortDirection = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sortBy = $columns[$sortColumnIndex] ?? 'id';
        $search = $request->input('search.value');

        $query = DB::table('periods');
        if ($tenantId && Schema::hasColumn('periods', 'tenant_id')) {
            $query->where('tenant_id', $tenantId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('periodName', 'like', "%$search%")
                    ->orWhere('day', 'like', "%$search%")
                    ->orWhere('start_time', 'like', "%$search%")
                    ->orWhere('end_time', 'like', "%$search%");
            });
        }

        $total = $query->count();
        $periods = $query->orderBy($sortBy, $sortDirection)
            ->offset((int)$request->input('start', 0))
            ->limit((int)$request->input('length', 10))
            ->get();

        $data = [];
        foreach ($periods as $p) {
            $data[] = [
                'id' => $p->id,
                'periodName' => $p->periodName,
                'day' => $p->day,
                'start_time' => $p->start_time,
                'end_time' => $p->end_time,
                'action' => '<button class="btn btn-sm btn-primary edit-button" data-id="' . $p->id . '" data-name="' . $p->periodName . '" data-day="' . $p->day . '" data-start="' . $p->start_time . '" data-end="' . $p->end_time . '">Edit</button> '
                . '<a href="' . request()->getBaseUrl() . '/deletePeriod/' . $p->id . '" class="btn btn-sm btn-danger">Delete</a>'
            ];
        }

        return response()->json([
            'draw' => (int)$request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function addPeriod(Request $request)
    {
        $this->ensurePeriodsTable();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        DB::table('periods')->insert([
            'tenant_id' => $tenantId,
            'school_id' => $request->input('school_id'),
            'periodName' => $request->input('periodName'),
            'day' => $request->input('day'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('message', 'Period added');
    }

    public function updatePeriod(Request $request)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $query = DB::table('periods')->where('id', (int)$request->input('id'));
        if ($tenantId && Schema::hasColumn('periods', 'tenant_id')) {
            $query->where('tenant_id', $tenantId);
        }
        $query->update([
            'school_id' => $request->input('school_id'),
            'periodName' => $request->input('periodName'),
            'day' => $request->input('day'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('message', 'Period updated');
    }

    public function deletePeriod($id)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $query = DB::table('periods')->where('id', (int)$id);
        if ($tenantId && Schema::hasColumn('periods', 'tenant_id')) {
            $query->where('tenant_id', $tenantId);
        }
        $query->delete();
        return redirect()->back()->with('message', 'Period deleted');
    }

    private function ensurePeriodsTable(): void
    {
        if (!\Schema::hasTable('periods')) {
            \Schema::create('periods', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
                $table->unsignedBigInteger('school_id')->nullable()->index();
                $table->string('periodName');
                $table->string('day')->nullable();
                $table->string('start_time')->nullable();
                $table->string('end_time')->nullable();
                $table->timestamps();
            });
        }
        foreach (['day', 'start_time', 'end_time'] as $col) {
            if (!\Schema::hasColumn('periods', $col)) {
                \Schema::table('periods', function (\Illuminate\Database\Schema\Blueprint $table) use ($col) {
                    $table->string($col)->nullable();
                });
            }
        }
        if (!\Schema::hasColumn('periods', 'tenant_id')) {
            \Schema::table('periods', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
            });
        }
        if (!\Schema::hasColumn('periods', 'school_id')) {
            \Schema::table('periods', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable()->index();
            });
        }
    }



    public function schools()
    {
        $user = auth()->user();
        $userId = $user->id;
        $tenantSchoolId = $user->tenant_id ?? null; // primary school for this user (legacy link)
        $ownerEmail = $user->email;

        // Show only schools that this user "owns":
        //  - primary: created_by_user_id = current user
        //  - fallback (old data before this column existed):
        //      * id matches tenant_id, or
        //      * admin email == user email
        $query = DB::table('schools')
            ->select(
            'id',
            'schoolName',
            'schoolCity',
            'schoolAdminName',
            'schoolAdminEmail',
            'bank_name',
            'bank_branch',
            'bank_account_title',
            'bank_account_number',
            'bank_iban'
        )
            ->where(function ($q) use ($userId, $tenantSchoolId, $ownerEmail) {
            // New records: explicit ownership
            $q->where('created_by_user_id', $userId);

            // Backward-compat for earlier rows without owner info
            $q->orWhere(function ($qq) use ($tenantSchoolId, $ownerEmail) {
                    if ($tenantSchoolId) {
                        $qq->where('id', $tenantSchoolId);
                    }
                    $qq->orWhere(function ($qqq) use ($ownerEmail) {
                            $qqq->whereNull('created_by_user_id')
                                ->where('schoolAdminEmail', $ownerEmail);
                        }
                        );
                    }
                    );
                })
            ->orderBy('schoolName', 'asc');

        $schoolList = $query->get();

        return view('schools', Compact('schoolList'));
    }

    public function addSchool(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['superadmin', 'admin'])) {
            return redirect()->back()->with('errorMessage', 'You are not allowed to add a school');
        }

        // Basic validation
        $request->validate([
            'schoolName' => 'required|string|max:255',
            'schoolCity' => 'required|string|max:255',
            'schoolAdminName' => 'required|string|max:255',
            'schoolAdminEmail' => 'required|email|max:255',
            'schoolAdminPassword' => 'required|string|min:6',
        ]);

        // Prevent duplicate admin email (avoid breaking unique index and orphan schools)
        $emailExists = DB::table('users')->where('email', $request->schoolAdminEmail)->exists();
        if ($emailExists) {
            return redirect()->back()->with('errorMessage', 'This admin email is already taken. Please use a different email.');
        }

        DB::beginTransaction();
        try {
            $schoolId = DB::table('schools')->insertGetId([
                'created_by_user_id' => auth()->id(),
                'schoolName' => $request->schoolName,
                'schoolCity' => $request->schoolCity,
                'schoolAdminName' => $request->schoolAdminName,
                'schoolAdminEmail' => $request->schoolAdminEmail,
                'schoolAdminPassword' => Hash::make($request->schoolAdminPassword),
                // bank fields
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'bank_account_title' => $request->bank_account_title,
                'bank_account_number' => $request->bank_account_number,
                'bank_iban' => $request->bank_iban,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->insert([
                'school_id' => $schoolId,
                'role' => 'admin',
                'name' => $request->schoolAdminName,
                'password' => Hash::make($request->schoolAdminPassword),
                'email' => $request->schoolAdminEmail,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('message', 'School Added Successfully');
        }
        catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('errorMessage', 'Failed to add school: ' . $e->getMessage());
        }
    }

    public function getSchools(Request $request)
    {
        Log::info('getSchools called', [
            'user_id' => auth()->id(),
            'params' => $request->all()
        ]);
        $columns = ['id', 'schoolName', 'schoolCity', 'schoolAdminName', 'schoolAdminEmail']; // Define columns to be selected

        // Sorting
        $sortColumnIndex = $request->input('order.0.column', 0);
        $sortDirection = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sortBy = isset($columns[$sortColumnIndex]) ? $columns[$sortColumnIndex] : 'id';

        // Searching
        $searchValue = $request->input('search.value');

        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;

        $baseQuery = DB::table('schools')
            ->select('id', 'schoolName', 'schoolCity', 'schoolAdminName', 'schoolAdminEmail')
            ->when($user->role !== 'superadmin' && $tenantId, function ($q) use ($tenantId) {
            $q->where('id', $tenantId);
        })
            ->when($searchValue, function ($q) use ($searchValue) {
            $q->where(function ($qq) use ($searchValue) {
                    $qq->where('schoolName', 'like', '%' . $searchValue . '%')
                        ->orWhere('schoolCity', 'like', '%' . $searchValue . '%')
                        ->orWhere('schoolAdminName', 'like', '%' . $searchValue . '%')
                        ->orWhere('schoolAdminEmail', 'like', '%' . $searchValue . '%');
                }
                );
            });

        // Total records scoped for user
        $totalRecords = ($user->role === 'superadmin')
            ?DB::table('schools')->count()
            : ($tenantId ?DB::table('schools')->where('id', $tenantId)->count() : 0);
        // Filtered count
        $filteredRecords = (clone $baseQuery)->count();

        $perPage = max(1, (int)$request->input('length', 10));
        $page = max(1, (int)floor($request->input('start', 0) / $perPage) + 1);

        try {
            $schools = (clone $baseQuery)
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage, ['*'], 'page', $page);
        }
        catch (\Throwable $e) {
            Log::error('getSchools error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        // Data format for DataTables
        $data = [];
        $serialNumber = ($page - 1) * $perPage + 1;
        foreach ($schools as $school) {
            $actionButton = sprintf(
                '<button type="button" class="btn btn-primary edit-button" data-toggle="modal" data-target="#editModal" data-id="%s" data-name="%s" data-schoolCity="%s" data-schoolAdminName="%s" data-schoolAdminEmail="%s"> Edit </button>',
                $school->id,
                $school->schoolName,
                $school->schoolCity,
                $school->schoolAdminName,
                $school->schoolAdminEmail
            );

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
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'data' => $data,
        ];

        return response()->json($response);
    }

    public function academicYear()
    {

        // Get all academic years with their data
        $academicYearList = DB::table('academicYears')
            ->select('id', 'academicYear', 'start_date', 'end_date', 'is_active')
            ->orderBy('academicYear', 'desc')
            ->get();

        return view('academicYear', Compact('academicYearList'));
    }
    public function getAcademicYear(Request $request)
    {

        // Define sortable columns that actually exist
        $columns = ['id', 'academicYear', 'is_active'];
        $totalData = DB::table('academicYears')->count();

        // Sorting
        $sortColumnIndex = $request->input('order.0.column');
        $sortDirection = $request->input('order.0.dir');
        $sortBy = $columns[$sortColumnIndex] ?? 'id';

        // Searching
        $searchValue = $request->input('search.value');
        $classes = DB::table('academicYears')
            ->select('id', 'academicYear', 'start_date', 'end_date', 'is_active')
            ->when($searchValue, function ($query) use ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                    $q->where('academicYear', 'like', '%' . $searchValue . '%')
                        ->orWhere('is_active', 'like', '%' . $searchValue . '%');
                }
                );
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate($request->input('length'));

        // Data format for DataTables
        $data = [];
        foreach ($classes as $class) {
            // Derive month/year parts from start_date/end_date for the edit modal
            $fromMonth = $class->start_date ?Carbon::parse($class->start_date)->format('F') : '';
            $fromYear = $class->start_date ?Carbon::parse($class->start_date)->format('Y') : '';
            $toMonth = $class->end_date ?Carbon::parse($class->end_date)->format('F') : '';
            $toYear = $class->end_date ?Carbon::parse($class->end_date)->format('Y') : '';

            $actionButton = '<button type="button" class="btn btn-primary edit-button" data-bs-toggle="modal" data-bs-target="#editModal" 
                  data-id="' . $class->id . '" 
                  data-name="' . $class->academicYear . '"
                  data-fromMonth="' . $fromMonth . '"
                  data-fromYear="' . $fromYear . '"
                  data-toMonth="' . $toMonth . '"
                  data-toYear="' . $toYear . '"
                  data-status="' . $class->is_active . '"
                  data-is_active="' . $class->is_active . '"> Edit </button>';

            $rowData = [
                'id' => $class->id,
                'academicYear' => $class->academicYear,
                'is_active' => $class->is_active,
                // Use is_active as a stand-in for status in the table
                'status' => $class->is_active,
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

    public function updateAcademicYear(Request $request)
    {

        // Log all request data for debugging
        \Log::info('updateAcademicYear called with data:', $request->all());

        try {
            // Validate the request
            $validatedData = $request->validate([
                'id' => 'required|integer',
                'academicYear' => 'required|string|max:255',
                'fromMonth' => 'required|string',
                'fromYear' => 'required|integer',
                'toMonth' => 'required|string',
                'toYear' => 'required|integer',
                'is_active' => 'required|in:yes,no,closed'
            ]);

            \Log::info('Validation passed:', $validatedData);

            // Define month mapping
            $monthNumbers = [
                'January' => 1,
                'February' => 2,
                'March' => 3,
                'April' => 4,
                'May' => 5,
                'June' => 6,
                'July' => 7,
                'August' => 8,
                'September' => 9,
                'October' => 10,
                'November' => 11,
                'December' => 12,
            ];

            \Log::info('Month mapping lookup:', [
                'fromMonth' => $request->fromMonth,
                'toMonth' => $request->toMonth,
                'fromMonthNumber' => $monthNumbers[$request->fromMonth] ?? 'NOT_FOUND',
                'toMonthNumber' => $monthNumbers[$request->toMonth] ?? 'NOT_FOUND'
            ]);

            // Convert month names to numbers
            $fromMonthNumber = $monthNumbers[$request->fromMonth];
            $toMonthNumber = $monthNumbers[$request->toMonth];

            // Create date strings and Carbon instances
            $fromDate = Carbon::createFromFormat('n-Y', $fromMonthNumber . '-' . $request->fromYear)->startOfMonth();
            $toDate = Carbon::createFromFormat('n-Y', $toMonthNumber . '-' . $request->toYear)->endOfMonth();

            \Log::info('Date conversion:', [
                'fromDate' => $fromDate->toDateString(),
                'toDate' => $toDate->toDateString()
            ]);

            // If setting this academic year as active, set all others to 'no'
            if ($request->is_active === 'yes') {
                \Log::info('Setting all other academic years to inactive');
                $resetResult = DB::table('academicYears')->update(['is_active' => 'no']);
                \Log::info('Reset result:', ['affected_rows' => $resetResult]);
            }

            \Log::info('About to update academic year with ID:', $request->id);

            // Update the academic year record
            $updated = DB::table('academicYears')
                ->where('id', $request->id)
                ->update([
                'academicYear' => $request->academicYear,
                'start_date' => $fromDate->toDateString(),
                'end_date' => $toDate->toDateString(),
                'is_active' => $request->is_active,
                'updated_at' => now(),
            ]);

            \Log::info('Update operation result:', ['updated_rows' => $updated]);

            if ($updated) {
                \Log::info('Academic year updated successfully');
                return redirect()->back()->with('message', 'Academic Year Updated Successfully');
            }
            else {
                \Log::warning('No rows were updated');
                return redirect()->back()->with('errorMessage', 'Academic Year not found or no changes made');
            }
        }
        catch (\Exception $e) {
            \Log::error('Exception in updateAcademicYear:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('errorMessage', 'Error updating academic year: ' . $e->getMessage());
        }
    }

    public function getSchool($id)
    {
        $user = auth()->user();
        $userId = $user->id;
        $tenantSchoolId = $user->tenant_id ?? null;
        $ownerEmail = $user->email;

        try {
            $query = DB::table('schools')
                ->select(
                'id',
                'schoolName',
                'schoolCity',
                'schoolAdminName',
                'schoolAdminEmail',
                'bank_name',
                'bank_branch',
                'bank_account_title',
                'bank_account_number',
                'bank_iban'
            )
                ->where('id', (int)$id);

            // If not superadmin, restrict to owned schools
            if ($user->role !== 'superadmin') {
                $query->where(function ($q) use ($userId, $tenantSchoolId, $ownerEmail) {
                    $q->where('created_by_user_id', $userId)
                        ->orWhere(function ($qq) use ($tenantSchoolId, $ownerEmail) {
                        if ($tenantSchoolId) {
                            $qq->where('id', $tenantSchoolId);
                        }
                        $qq->orWhere(function ($qqq) use ($ownerEmail) {
                                    $qqq->whereNull('created_by_user_id')
                                        ->where('schoolAdminEmail', $ownerEmail);
                                }
                                );
                            }
                            );
                        });
            }

            $school = $query->first();

            if (!$school) {
                return response()->json(['ok' => false, 'error' => 'Not found or access denied'], 404);
            }
            return response()->json(['ok' => true, 'data' => $school]);
        }
        catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateSchool(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|integer',
            'schoolName' => 'required|string|max:255',
            'schoolCity' => 'required|string|max:255',
            'schoolAdminName' => 'required|string|max:255',
            'schoolAdminEmail' => 'required|email|max:255',
        ]);

        $user = auth()->user();
        $userId = $user->id;
        $tenantSchoolId = $user->tenant_id ?? null;
        $ownerEmail = $user->email;

        try {
            $query = DB::table('schools')->where('id', $request->id);

            // If not superadmin, restrict to owned schools
            if ($user->role !== 'superadmin') {
                $query->where(function ($q) use ($userId, $tenantSchoolId, $ownerEmail) {
                    $q->where('created_by_user_id', $userId)
                        ->orWhere(function ($qq) use ($tenantSchoolId, $ownerEmail) {
                        if ($tenantSchoolId) {
                            $qq->where('id', $tenantSchoolId);
                        }
                        $qq->orWhere(function ($qqq) use ($ownerEmail) {
                                    $qqq->whereNull('created_by_user_id')
                                        ->where('schoolAdminEmail', $ownerEmail);
                                }
                                );
                            }
                            );
                        });
            }

            // Check if school exists and user has access before updating
            if (!$query->exists()) {
                return redirect()->back()->with('errorMessage', 'School not found or access denied');
            }

            // Update the school record
            $query->update([
                'schoolName' => $request->schoolName,
                'schoolCity' => $request->schoolCity,
                'schoolAdminName' => $request->schoolAdminName,
                'schoolAdminEmail' => $request->schoolAdminEmail,
                // bank fields
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'bank_account_title' => $request->bank_account_title,
                'bank_account_number' => $request->bank_account_number,
                'bank_iban' => $request->bank_iban,
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('message', 'School Updated Successfully');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('errorMessage', 'Error updating school: ' . $e->getMessage());
        }
    }

    public function addAcademicYear(Request $request)
    {

        // Define an associative array to map month names to their numerical equivalents
        $monthNumbers = [
            'JANUARY' => 1,
            'FEBRUARY' => 2,
            'MARCH' => 3,
            'APRIL' => 4,
            'MAY' => 5,
            'JUNE' => 6,
            'JULY' => 7,
            'AUGUST' => 8,
            'SEPTEMBER' => 9,
            'OCTOBER' => 10,
            'NOVEMBER' => 11,
            'DECEMBER' => 12,
        ];

        // Convert month names to their numerical representations
        if (!isset($monthNumbers[strtoupper($request->fromMonth)]) || !isset($monthNumbers[strtoupper($request->toMonth)])) {
            return redirect()->back()->with(['errorMessage' => 'Invalid month selected.']);
        }

        $fromMonthNumber = $monthNumbers[strtoupper($request->fromMonth)];
        $toMonthNumber = $monthNumbers[strtoupper($request->toMonth)];

        // Construct the date strings separately for 'Month Year' format
        $fromDateString = $fromMonthNumber . '-' . $request->fromYear;
        $toDateString = $toMonthNumber . '-' . $request->toYear;

        // Parse the date strings into Carbon instances
        $fromDate = Carbon::createFromFormat('n-Y', $fromDateString)->startOfMonth();
        $toDate = Carbon::createFromFormat('n-Y', $toDateString)->endOfMonth();

        // Calculate the difference in months between the dates
        $monthDifference = $toDate->diffInMonths($fromDate) + 1;

        // Check if the difference is exactly 12 months
        if ($monthDifference !== 12) {
            return redirect()->back()->with(['errorMessage' => 'The academic year must be exactly 12 months.']);
        }

        // Ensure that the fromMonth is April
        if (strtoupper($request->fromMonth) !== 'APRIL') {
            return redirect()->back()->with(['errorMessage' => 'The academic year must START from APRIL.']);
        }

        // Ensure that the toMonth is March
        if (strtoupper($request->toMonth) !== 'MARCH') {
            return redirect()->back()->with(['errorMessage' => 'The academic year must END in MARCH.']);
        }

        // Calculate the difference in years between the fromYear and toYear
        $yearDifference = (int)$request->toYear - (int)$request->fromYear;

        // Check if the difference in years is exactly 1
        if ($yearDifference !== 1) {
            return redirect()->back()->with(['errorMessage' => 'The difference between startingYear and endingYear must be exactly 1 year.']);
        }

        // Check if there are any existing records where the new record overlaps
        // Overlap condition: existing.start_date <= new.toDate AND existing.end_date >= new.fromDate
        $overlappingRecords = DB::table('academicYears')
            ->where(function ($query) use ($fromDate, $toDate) {
            $query->where('start_date', '<=', $toDate->toDateString())
                ->where('end_date', '>=', $fromDate->toDateString());
        })
            ->exists();

        if ($overlappingRecords) {
            return redirect()->back()->with(['errorMessage' => 'The academic year overlaps with an existing record.']);
        }

        // Insert using the actual schema (start_date/end_date)
        DB::table('academicYears')->insert([
            'academicYear' => $request->academicYear,
            'start_date' => $fromDate->toDateString(),
            'end_date' => $toDate->toDateString(),
            'is_active' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('message', 'Academic Year Added Successfully');
    }

    public function deleteTimeTable($teacherId, $day, $period)
    {

        // echo $teacherId;
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $query = DB::table('timetables')->where('id', $teacherId);
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        $query->delete();

        return redirect()->back()->with('message', 'Period Deleted Successfully');
    }
}
