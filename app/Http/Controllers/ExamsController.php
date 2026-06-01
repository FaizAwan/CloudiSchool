<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\teachers;
use App\Models\students;
use App\Models\ExamQuestion;
use App\Models\McqOption;
use App\Models\StudentExamAttempt;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of exams
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Exam::with(['examType', 'subject', 'teacher']);

        // Filter based on user role
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        } elseif ($user->role === 'student') {
            $student = students::where('user_id', $user->id)->first();
            if ($student) {
                $query->where('class_id', $student->class)
                      ->where('status', 'published');
            }
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('exam_type')) {
            $query->where('exam_type_id', $request->exam_type);
        }

        if ($request->filled('subject')) {
            $query->where('subject_id', $request->subject);
        }

        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }

        $exams = $query->latest()->paginate(15);

        $examTypes = ExamType::active()->get();
        $subjects = Subject::active()->get();
        $classes = Classes::all();

        return view('exams.index', compact('exams', 'examTypes', 'subjects', 'classes'));
    }

    /**
     * Show the form for creating a new exam
     */
    public function create()
    {
        $this->authorize('create', Exam::class);
        // Ensure baseline exam types exist for the tenant
        if (ExamType::count() === 0) {
            try {
                ExamType::create([
                    'school_id' => 1,
                    'exam_type_name' => 'Objective Type',
                    'description' => 'Objective questions (MCQ/True-False/Fill-in-the-blank)',
                    'status' => 'active',
                ]);
                ExamType::create([
                    'school_id' => 1,
                    'exam_type_name' => 'Subjective Type',
                    'description' => 'Descriptive/long answer questions requiring manual grading',
                    'status' => 'active',
                ]);
            } catch (\Throwable $e) {
                // ignore if creation fails silently
            }
        }

        $examTypes = ExamType::active()->orderBy('exam_type_name')->get();
        $subjects = Subject::active()->get();
        $classes = Classes::all();
        $teachers = teachers::all();

        return view('exams.create', compact('examTypes', 'subjects', 'classes', 'teachers'));
    }

    /**
     * Store a newly created exam
     */
    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

        $validator = Validator::make($request->all(), [
            'exam_name' => 'required|string|max:200',
            'exam_type_id' => 'required|exists:exam_types,id',
            'class_id' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'exam_date' => 'required|date|after_or_equal:today',
            'exam_time' => 'required',
            'duration_minutes' => 'required|integer|min:15|max:300',
            'total_marks' => 'required|integer|min:1|max:1000',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
            'instructions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $exam = new Exam($request->all());
            $exam->school_id = 1; // Default school ID
            $exam->session = '2024-25'; // Current session
            $exam->created_by = Auth::id();
            
            // Get class name
            $class = Classes::find($request->class_id);
            $exam->class_name = $class ? $class->class_name : $request->class_id;
            
            $exam->save();

            DB::commit();
            return redirect()->route('exams.show', $exam->id)
                           ->with('success', 'Exam created successfully! Now add questions to complete the exam.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to create exam. Please try again.')
                           ->withInput();
        }
    }

    /**
     * Display the specified exam
     */
    public function show($id)
    {
        $exam = Exam::with(['examType', 'subject', 'teacher', 'questions.mcqOptions'])
                    ->findOrFail($id);

        $this->authorize('view', $exam);

        $user = Auth::user();
        $teacher = teachers::where('user_id', $user->id)->first();
        $canEdit = $user->role === 'admin' || 
                  ($user->role === 'teacher' && $teacher && $exam->teacher_id === $teacher->id);

        // Get student's attempt if student is viewing
        $attempt = null;
        if ($user->role === 'student') {
            $student = students::where('user_id', $user->id)->first();
            if ($student) {
                $attempt = StudentExamAttempt::where('exam_id', $exam->id)
                                           ->where('student_id', $student->id)
                                           ->first();
            }
        }

        // Get exam statistics for teachers/admin
        $statistics = null;
        if (in_array($user->role, ['admin', 'teacher'])) {
            $statistics = [
                'total_attempts' => StudentExamAttempt::where('exam_id', $exam->id)->count(),
                'completed_attempts' => StudentExamAttempt::where('exam_id', $exam->id)
                                                        ->whereIn('status', ['submitted', 'auto_submitted', 'graded'])
                                                        ->count(),
                'average_percentage' => ExamResult::where('exam_id', $exam->id)->avg('percentage') ?? 0,
                'pass_rate' => ExamResult::where('exam_id', $exam->id)->where('status', 'pass')->count()
            ];
        }

        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'id' => $exam->id,
                'exam_name' => $exam->exam_name,
                'exam_type' => $exam->examType,
                'subject' => $exam->subject,
                'class_id' => $exam->class_id,
                'class_name' => $exam->class_name,
                'exam_date' => $exam->exam_date,
                'exam_time' => $exam->exam_time,
                'duration_minutes' => $exam->duration_minutes,
                'total_marks' => $exam->total_marks,
                'passing_marks' => $exam->passing_marks,
                'total_questions' => $exam->questions->count(),
                'mcq_questions' => $exam->questions->where('question_type', 'mcq')->count(),
                'short_questions' => $exam->questions->where('question_type', 'short_answer')->count(),
                'long_questions' => $exam->questions->where('question_type', 'essay')->count(),
                'instructions' => $exam->instructions,
                'status' => $exam->status,
                'statistics' => $statistics,
                'can_edit' => $canEdit
            ]);
        }
        
        return view('exams.show', compact('exam', 'canEdit', 'attempt', 'statistics'));
    }

    /**
     * Show the form for editing the specified exam
     */
    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);

        $examTypes = ExamType::active()->get();
        $subjects = Subject::active()->get();
        $classes = Classes::all();
        $teachers = teachers::all();

        return view('exams.edit', compact('exam', 'examTypes', 'subjects', 'classes', 'teachers'));
    }

    /**
     * Update the specified exam
     */
    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);

        $validator = Validator::make($request->all(), [
            'exam_name' => 'required|string|max:200',
            'exam_type_id' => 'required|exists:exam_types,id',
            'class_id' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'exam_date' => 'required|date',
            'exam_time' => 'required',
            'duration_minutes' => 'required|integer|min:15|max:300',
            'total_marks' => 'required|integer|min:1|max:1000',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
            'instructions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $exam->fill($request->all());
            
            // Get class name
            $class = Classes::find($request->class_id);
            $exam->class_name = $class ? $class->class_name : $request->class_id;
            
            $exam->save();

            DB::commit();
            return redirect()->route('exams.show', $exam->id)
                           ->with('success', 'Exam updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to update exam. Please try again.')
                           ->withInput();
        }
    }

    /**
     * Remove the specified exam
     */
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('delete', $exam);

        DB::beginTransaction();
        try {
            // Check if exam has attempts
            $attemptCount = StudentExamAttempt::where('exam_id', $exam->id)->count();
            
            if ($attemptCount > 0) {
                return redirect()->back()
                               ->with('error', 'Cannot delete exam with existing student attempts.');
            }

            $exam->delete();

            DB::commit();
            return redirect()->route('exams.index')
                           ->with('success', 'Exam deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to delete exam. Please try again.');
        }
    }

    /**
     * Publish/Unpublish an exam
     */
    public function toggleStatus($id)
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);

        DB::beginTransaction();
        try {
            if ($exam->status === 'draft') {
                // Validate exam before publishing
                if ($exam->questions()->count() === 0) {
                    return redirect()->back()
                                   ->with('error', 'Cannot publish exam without questions.');
                }

                $exam->status = 'published';
                $message = 'Exam published successfully!';
            } elseif ($exam->status === 'published') {
                $exam->status = 'draft';
                $message = 'Exam unpublished successfully!';
            } else {
                return redirect()->back()
                               ->with('error', 'Cannot change status of completed exam.');
            }

            $exam->save();

            DB::commit();
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to update exam status. Please try again.');
        }
    }

    /**
     * Duplicate an exam
     */
    public function duplicate($id)
    {
        $exam = Exam::with(['questions.mcqOptions'])->findOrFail($id);
        $this->authorize('create', Exam::class);

        DB::beginTransaction();
        try {
            // Create new exam
            $newExam = $exam->replicate();
            $newExam->exam_name = $exam->exam_name . ' (Copy)';
            $newExam->status = 'draft';
            $newExam->created_by = Auth::id();
            $newExam->save();

            // Copy questions
            foreach ($exam->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->exam_id = $newExam->id;
                $newQuestion->save();

                // Copy MCQ options
                foreach ($question->mcqOptions as $option) {
                    $newOption = $option->replicate();
                    $newOption->question_id = $newQuestion->id;
                    $newOption->save();
                }
            }

            DB::commit();
            return redirect()->route('exams.show', $newExam->id)
                           ->with('success', 'Exam duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to duplicate exam. Please try again.');
        }
    }

    /**
     * Get exam results for a specific exam
     */
    public function results($id)
    {
        $exam = Exam::with(['subject', 'examType'])->findOrFail($id);
        $this->authorize('viewResults', $exam);

        $results = ExamResult::with(['student', 'attempt'])
                            ->where('exam_id', $exam->id)
                            ->orderBy('position')
                            ->get();

        $statistics = [
            'total_students' => $results->count(),
            'passed' => $results->where('status', 'pass')->count(),
            'failed' => $results->where('status', 'fail')->count(),
            'absent' => $results->where('status', 'absent')->count(),
            'average_percentage' => $results->avg('percentage') ?? 0,
            'highest_marks' => $results->max('obtained_marks') ?? 0,
            'lowest_marks' => $results->min('obtained_marks') ?? 0,
        ];

        return view('exams.results', compact('exam', 'results', 'statistics'));
    }

    /**
     * Export exam results to Excel/PDF
     */
    public function exportResults($id, $format = 'excel')
    {
        $exam = Exam::with(['subject', 'examType', 'questions.mcqOptions'])->findOrFail($id);
        $this->authorize('viewResults', $exam);

        if ($format === 'csv' || $format === 'excel') {
            return $this->exportToCSV($exam);
        } elseif ($format === 'pdf') {
            return $this->exportToPDF($exam);
        }
        
        return redirect()->back()->with('error', 'Invalid export format.');
    }
    
    /**
     * Export exam data to CSV format
     */
    private function exportToCSV($exam)
    {
        $filename = 'exam_' . $exam->id . '_' . str_replace(' ', '_', $exam->exam_name) . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($exam) {
            $file = fopen('php://output', 'w');
            
            // Add exam information header
            fputcsv($file, ['Exam Report']);
            fputcsv($file, ['Exam Name', $exam->exam_name]);
            fputcsv($file, ['Subject', $exam->subject->subject_name ?? 'N/A']);
            fputcsv($file, ['Class', $exam->class_name]);
            fputcsv($file, ['Date', $exam->exam_date]);
            fputcsv($file, ['Duration', $exam->duration_minutes . ' minutes']);
            fputcsv($file, ['Total Marks', $exam->total_marks]);
            fputcsv($file, ['Passing Marks', $exam->passing_marks]);
            fputcsv($file, []);
            
            // Add questions header
            fputcsv($file, ['Questions']);
            fputcsv($file, ['Question #', 'Type', 'Question Text', 'Marks']);
            
            $questionNumber = 1;
            foreach ($exam->questions as $question) {
                fputcsv($file, [
                    $questionNumber++,
                    ucfirst($question->question_type),
                    strip_tags($question->question_text),
                    $question->marks
                ]);
                
                // Add MCQ options if available
                if ($question->question_type === 'mcq') {
                    foreach ($question->mcqOptions as $option) {
                        fputcsv($file, [
                            '',
                            'Option ' . $option->option_letter,
                            $option->option_text,
                            $option->is_correct ? '(Correct)' : ''
                        ]);
                    }
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export exam data to PDF format (basic implementation)
     */
    private function exportToPDF($exam)
    {
        // For now, return a simple text file since we don't have PDF libraries installed
        $filename = 'exam_' . $exam->id . '_' . str_replace(' ', '_', $exam->exam_name) . '.txt';
        
        $headers = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $content = "EXAM REPORT\n";
        $content .= "===================\n\n";
        $content .= "Exam Name: {$exam->exam_name}\n";
        $content .= "Subject: " . ($exam->subject->subject_name ?? 'N/A') . "\n";
        $content .= "Class: {$exam->class_name}\n";
        $content .= "Date: {$exam->exam_date}\n";
        $content .= "Duration: {$exam->duration_minutes} minutes\n";
        $content .= "Total Marks: {$exam->total_marks}\n";
        $content .= "Passing Marks: {$exam->passing_marks}\n\n";
        
        $content .= "QUESTIONS:\n";
        $content .= "===================\n\n";
        
        $questionNumber = 1;
        foreach ($exam->questions as $question) {
            $content .= "Question {$questionNumber}: " . strip_tags($question->question_text) . "\n";
            $content .= "Type: " . ucfirst($question->question_type) . "\n";
            $content .= "Marks: {$question->marks}\n";
            
            if ($question->question_type === 'mcq') {
                foreach ($question->mcqOptions as $option) {
                    $correct = $option->is_correct ? ' (Correct Answer)' : '';
                    $content .= "  {$option->option_letter}. {$option->option_text}{$correct}\n";
                }
            }
            
            $content .= "\n";
            $questionNumber++;
        }
        
        return response($content, 200, $headers);
    }
    
    /**
     * Display teacher exam dashboard
     */
    public function teacherDashboard()
    {
        $user = Auth::user();
        
        // Ensure only teachers can access this
        if ($user->role !== 'teacher') {
            abort(403, 'Unauthorized access.');
        }
        
        $teacher = teachers::where('user_id', $user->id)->first();
        
        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }
        
        // Get teacher's exams statistics
        $totalExams = Exam::where('teacher_id', $teacher->id)->count();
        $activeExams = Exam::where('teacher_id', $teacher->id)->where('status', 'published')->count();
        $draftExams = Exam::where('teacher_id', $teacher->id)->where('status', 'draft')->count();
        
        // Get recent exams (last 5)
        $recentExams = Exam::with(['examType', 'subject'])
                          ->where('teacher_id', $teacher->id)
                          ->latest()
                          ->limit(5)
                          ->get();
        
        // Get upcoming exams (next 5)
        $upcomingExams = Exam::with(['examType', 'subject'])
                            ->where('teacher_id', $teacher->id)
                            ->where('exam_date', '>=', now())
                            ->where('status', 'published')
                            ->orderBy('exam_date')
                            ->limit(5)
                            ->get();
        
        // Get exam attempts statistics
        $totalAttempts = StudentExamAttempt::whereHas('exam', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->count();
        
        // Get average performance
        $avgPerformance = StudentExamAttempt::whereHas('exam', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->avg('percentage') ?? 0;
        
        // Get subject-wise performance
        $subjectWisePerformance = collect();
        $examsBySubject = Exam::with('subject')
                             ->where('teacher_id', $teacher->id)
                             ->get()
                             ->groupBy('subject_id');
        
        foreach ($examsBySubject as $subjectId => $exams) {
            $examIds = $exams->pluck('id');
            $avgPerf = StudentExamAttempt::whereIn('exam_id', $examIds)->avg('percentage') ?? 0;
            $subjectWisePerformance->push([
                'subject_name' => $exams->first()->subject->subject_name ?? 'Unknown',
                'avg_percentage' => round($avgPerf, 1)
            ]);
        }
        
        // Get pending grading count
        $pendingGrading = StudentExamAttempt::whereHas('exam', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->where('status', 'submitted')->count();
        
        return view('teacher.exams.dashboard', compact(
            'totalExams',
            'activeExams', 
            'draftExams',
            'recentExams',
            'upcomingExams',
            'totalAttempts',
            'avgPerformance',
            'subjectWisePerformance',
            'pendingGrading'
        ));
    }
}
