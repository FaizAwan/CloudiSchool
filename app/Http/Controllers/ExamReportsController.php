<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\students;
use App\Models\StudentExamAttempt;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the exam reports dashboard
     */
    public function index()
    {
        // Check if this is an AJAX request asking for JSON
        if (request()->wantsJson() || request()->ajax()) {
            // If it's an AJAX request, return the completed exams as JSON
            $examQuery = Exam::with(['subject']);
            $completedExams = $examQuery->where('status', 'completed')->get();
            return response()->json($completedExams);
        }
        
        // Force HTML response for web requests
        request()->headers->set('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        
        $user = Auth::user();
        
        // Get exams based on user role
        $examQuery = Exam::with(['subject']);
        
        if ($user->role === 'teacher') {
            $teacher = \App\Models\teachers::where('user_id', $user->id)->first();
            if ($teacher) {
                $examQuery->where('teacher_id', $teacher->id);
            }
        } elseif ($user->role === 'admin') {
            // Admin can see all exams for their school
            $examQuery->where('school_id', 1);
        }
        // Superadmin can see all exams
        
        $allExams = $examQuery->get();
        $completedExams = $examQuery->where('status', 'completed')->get();
        
        // Get statistics
        $totalExams = $allExams->count();
        $completedExamsCount = $completedExams->count();
        
        // Calculate average performance (placeholder calculation)
        $avgPercentage = $this->calculateAveragePerformance($completedExams);
        
        // Get total students who participated
        $totalStudents = $this->getTotalParticipatingStudents($completedExams);
        
        // Get recent reports (placeholder - would come from a reports table)
        $recentReports = collect(); // Empty for now
        
        // Ensure we return an HTML view, not JSON
        return response()->view('exam-reports.index', [
            'totalExams' => $totalExams,
            'completedExams' => $completedExams,
            'avgPercentage' => $avgPercentage,
            'totalStudents' => $totalStudents,
            'recentReports' => $recentReports,
            'subjects' => Subject::active()->get(),
            'students' => students::active()->with('class')->orderBy('studentName')->get()
        ], 200, ['Content-Type' => 'text/html']);
    }

    /**
     * Show Student Results listing with filters and CSV export
     */
    public function results(Request $request)
    {
        // Filters
        $examId     = $request->get('exam_id');
        $classId    = $request->get('class_id');
        $studentId  = $request->get('student_id');
        $status     = $request->get('status'); // pass, fail, absent
        $fromDate   = $request->get('from_date');
        $toDate     = $request->get('to_date');

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $resultsQuery = DB::table('exam_results as er')
            ->join('exams as e', 'er.exam_id', '=', 'e.id')
            ->join('students as s', 'er.student_id', '=', 's.id')
            ->leftJoin('subjects as sub', 'e.subject_id', '=', 'sub.id')
            ->leftJoin('classes as c', 's.class_id', '=', 'c.id')
            ->when($tenantId, function($q) use ($tenantId){
                $q->where('er.tenant_id', $tenantId)
                  ->where('e.tenant_id', $tenantId)
                  ->where('s.tenant_id', $tenantId);
            })
            ->select([
                'er.id', 'er.exam_id', 'er.student_id', 'er.total_marks', 'er.obtained_marks', 'er.percentage', 'er.grade', 'er.position', 'er.status', 'er.graded_at',
                's.studentName as student_name', 's.grno',
                DB::raw("COALESCE(c.className, e.class_name) as class_name"),
                'e.exam_name', 'sub.subject_name'
            ]);

        if ($examId)    { $resultsQuery->where('er.exam_id', (int) $examId); }
        if ($classId)   { $resultsQuery->where('s.class_id', (int) $classId); }
        if ($studentId) { $resultsQuery->where('er.student_id', (int) $studentId); }
        if ($status)    { $resultsQuery->where('er.status', $status); }
        if ($fromDate)  { $resultsQuery->whereDate('er.graded_at', '>=', $fromDate); }
        if ($toDate)    { $resultsQuery->whereDate('er.graded_at', '<=', $toDate); }

        $resultsQuery->orderBy('er.graded_at', 'desc');

        // CSV export if requested
        if ($request->get('export') === 'csv') {
            $rows = $resultsQuery->get();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="student_results.csv"',
            ];
            $columns = ['GR No','Student Name','Class','Exam','Subject','Total Marks','Obtained','Percentage','Grade','Position','Status','Graded At'];

            $callback = function() use ($rows, $columns) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $columns);
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->grno,
                        $r->student_name,
                        $r->class_name,
                        $r->exam_name,
                        $r->subject_name,
                        $r->total_marks,
                        $r->obtained_marks,
                        $r->percentage,
                        $r->grade,
                        $r->position,
                        $r->status,
                        $r->graded_at,
                    ]);
                }
                fclose($out);
            };
            return response()->streamDownload($callback, 'student_results.csv', $headers);
        }

        // Paginate for UI
        $results = $resultsQuery->paginate(50)->withQueryString();

        // Dropdown data
        $classes  = DB::table('classes')->select('id','className')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->orderBy('className')->get();
        $exams    = DB::table('exams')->select('id','exam_name')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->orderBy('exam_date','desc')->get();
        $students = DB::table('students')->select('id','studentName')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->orderBy('studentName')->get();
        $subjects = DB::table('subjects')->select('id','subject_name')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->orderBy('subject_name')->get();

        return view('exam-reports.results', compact('results','classes','exams','students','subjects'));
    }

    /**
     * Basic analytics placeholder to satisfy existing route without breaking features
     */
    public function analytics(Request $request)
    {
        // Quick stats using existing data, kept lightweight
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $stats = [
            'results_count' => DB::table('exam_results')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->count(),
            'passed' => DB::table('exam_results')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->where('status','pass')->count(),
            'failed' => DB::table('exam_results')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->where('status','fail')->count(),
            'absent' => DB::table('exam_results')->when($tenantId, function($q) use ($tenantId){ $q->where('tenant_id', $tenantId); })->where('status','absent')->count(),
        ];
        return view('exam-reports.analytics', compact('stats'));
    }

    /**
     * Full-page Student Performance report (no modal)
     */
    public function studentPerformancePage(Request $request)
    {
        $reportData = [
            'student' => null,
            'date_range' => $request->input('date_range', 'last_month'),
            'total_exams' => 12,
            'exams_taken' => 12,
            'average_percentage' => 82.3,
            'improvement_trend' => 'improving',
            'best_subject' => 'Math',
        ];

        if ($request->filled('student_id')) {
            $student = students::with('class')->find($request->integer('student_id'));
            if ($student) {
                $reportData = $this->generateStudentPerformanceData($student, $request->all());
            }
        }

        return view('exam-reports.student-performance', compact('reportData'));
    }

    /**
     * Generate comparative analysis report
     */
    public function comparative(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'exam_ids' => 'required|array|min:2',
                'exam_ids.*' => 'exists:exams,id'
            ]);
            
            $exams = Exam::with(['subject'])->whereIn('id', $request->exam_ids)->get();
            
            // Generate comparative report data (placeholder)
            $reportData = $this->generateComparativeData($exams, $request->all());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $reportData,
                    'html' => view('exam-reports.partials.comparative-analysis', compact('reportData', 'exams'))->render()
                ]);
            }
            
            return view('exam-reports.comparative-analysis', compact('reportData', 'exams'));
        }
        
        return redirect()->route('exam-reports.index');
    }

    /**
     * Calculate average performance across all completed exams
     */
    private function calculateAveragePerformance($completedExams)
    {
        if ($completedExams->isEmpty()) {
            return 0;
        }
        
        // Placeholder calculation - would use actual exam results
        return 75; // Default average
    }
    
    /**
     * Get total number of students who participated in exams
     */
    private function getTotalParticipatingStudents($completedExams)
    {
        if ($completedExams->isEmpty()) {
            return 0;
        }
        
        // Placeholder - would count unique students from exam attempts
        return students::count(); // Default count
    }
    
    /**
     * Generate class performance data
     */
    private function generateClassPerformanceData($exam, $class = null)
    {
        // Placeholder data structure
        return [
            'exam' => $exam,
            'class' => $class,
            'total_students' => 30,
            'appeared_students' => 28,
            'passed_students' => 22,
            'failed_students' => 6,
            'average_marks' => 68.5,
            'highest_marks' => 95,
            'lowest_marks' => 35,
            'grade_distribution' => [
                'A+' => 3,
                'A' => 5,
                'B+' => 8,
                'B' => 6,
                'C+' => 4,
                'C' => 2,
                'F' => 6
            ]
        ];
    }
    
    /**
     * Generate student performance data
     */
    private function generateStudentPerformanceData($student, $params)
    {
        // Placeholder data structure
        return [
            'student' => $student,
            'date_range' => $params['date_range'],
            'total_exams' => 8,
            'exams_taken' => 7,
            'average_percentage' => 72.3,
            'improvement_trend' => 'improving',
            'subject_performance' => [
                ['subject' => 'Mathematics', 'average' => 78, 'exams' => 3],
                ['subject' => 'English', 'average' => 85, 'exams' => 2],
                ['subject' => 'Science', 'average' => 65, 'exams' => 2]
            ]
        ];
    }
    
    /**
     * Generate comparative analysis data
     */
    private function generateComparativeData($exams, $params)
    {
        // Placeholder data structure
        return [
            'exams' => $exams,
            'comparison_type' => $params['chart_type'] ?? 'bar',
            'data' => [
                'labels' => $exams->pluck('exam_name')->toArray(),
                'averages' => [75, 68, 82, 70],
                'participation' => [95, 87, 92, 89]
            ]
        ];
    }
}
