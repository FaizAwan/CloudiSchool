<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\students;
use App\Models\StudentExamAttempt;
use App\Models\StudentAnswer;
use App\Models\ExamResult;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StudentExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'student') {
                abort(403, 'Access denied. Students only.');
            }
            return $next($request);
        });
    }

    /**
     * Display available exams for student
     */
    public function index(Request $request)
    {
        $student = students::where('user_id', Auth::id())->firstOrFail();
        
        // Get all exams for this student's class
        $baseQuery = Exam::with(['examType', 'subject'])
                         ->where('class_id', $student->class)
                         ->where('status', 'published');
        
        // Available exams (current and future)
        $examsAvailable = (clone $baseQuery)
                         ->where('exam_date', '>=', now()->toDateString())
                         ->whereDoesntHave('attempts', function($query) use ($student) {
                             $query->where('student_id', $student->id)
                                   ->whereIn('status', ['submitted', 'auto_submitted', 'graded']);
                         })
                         ->orderBy('exam_date')
                         ->get();
        
        // Upcoming exams (future only)
        $examsUpcoming = (clone $baseQuery)
                        ->where('exam_date', '>', now()->toDateString())
                        ->orderBy('exam_date')
                        ->get();
        
        // Completed exams
        $completedAttempts = StudentExamAttempt::where('student_id', $student->id)
                                              ->whereIn('status', ['submitted', 'auto_submitted', 'graded'])
                                              ->pluck('exam_id');
        
        $examsCompleted = (clone $baseQuery)
                         ->whereIn('id', $completedAttempts)
                         ->orderBy('exam_date', 'desc')
                         ->get();
        
        // Get student's attempts
        $allExamIds = $examsAvailable->pluck('id')
                                   ->merge($examsUpcoming->pluck('id'))
                                   ->merge($examsCompleted->pluck('id'));
        
        $studentAttempts = StudentExamAttempt::where('student_id', $student->id)
                                           ->whereIn('exam_id', $allExamIds)
                                           ->get();
        
        // Get exam results
        $examResults = ExamResult::with(['exam.examType', 'exam.subject'])
                                ->where('student_id', $student->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Calculate statistics
        $availableExams = $examsAvailable->count();
        $completedExams = $examsCompleted->count();
        $upcomingExams = $examsUpcoming->count();
        $averageScore = $examResults->count() > 0 ? $examResults->avg('percentage') : 0;
        
        return view('student.exams.index', compact(
            'examsAvailable', 
            'examsUpcoming', 
            'examsCompleted', 
            'studentAttempts',
            'examResults',
            'availableExams',
            'completedExams', 
            'upcomingExams',
            'averageScore'
        ));
    }

    /**
     * Show exam details before starting
     */
    public function show($id)
    {
        $student = students::where('user_id', Auth::id())->firstOrFail();
        $exam = Exam::with(['examType', 'subject', 'questions'])
                   ->where('class_id', $student->class)
                   ->where('status', 'published')
                   ->findOrFail($id);

        // Check if student already has an attempt
        $attempt = StudentExamAttempt::where('exam_id', $exam->id)
                                   ->where('student_id', $student->id)
                                   ->first();

        // Check if exam is currently available
        $isAvailable = $this->isExamAvailable($exam);

        return view('student.exams.show', compact('exam', 'attempt', 'isAvailable'));
    }

    /**
     * Start an exam attempt
     */
    public function startExam($id)
    {
        $student = students::where('user_id', Auth::id())->firstOrFail();
        $exam = Exam::where('class_id', $student->class)
                   ->where('status', 'published')
                   ->findOrFail($id);

        // Check if exam is available
        if (!$this->isExamAvailable($exam)) {
            return redirect()->route('student.exams.show', $exam->id)
                           ->with('error', 'This exam is not currently available.');
        }

        // Check if student already has an active attempt
        $existingAttempt = StudentExamAttempt::where('exam_id', $exam->id)
                                           ->where('student_id', $student->id)
                                           ->where('status', 'started')
                                           ->first();

        if ($existingAttempt) {
            return redirect()->route('student.exams.take', $exam->id);
        }

        // Check if student already completed the exam
        $completedAttempt = StudentExamAttempt::where('exam_id', $exam->id)
                                            ->where('student_id', $student->id)
                                            ->whereIn('status', ['submitted', 'auto_submitted', 'graded'])
                                            ->first();

        if ($completedAttempt) {
            return redirect()->route('student.exams.result', $exam->id)
                           ->with('info', 'You have already completed this exam.');
        }

        DB::beginTransaction();
        try {
            // Create new exam attempt
            $attempt = new StudentExamAttempt([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'attempt_number' => 1,
                'start_time' => now(),
                'total_questions' => $exam->questions()->count(),
                'status' => 'started',
                'ip_address' => request()->ip(),
                'browser_info' => request()->header('User-Agent')
            ]);
            $attempt->save();

            DB::commit();
            return redirect()->route('student.exams.take', $exam->id)
                           ->with('success', 'Exam started successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to start exam. Please try again.');
        }
    }

    /**
     * Take the exam (exam interface)
     */
    public function takeExam($id)
    {
        $student = students::where('user_id', Auth::id())->firstOrFail();
        $exam = Exam::with(['questions.mcqOptions'])
                   ->where('class_id', $student->class)
                   ->where('status', 'published')
                   ->findOrFail($id);

        // Get active attempt
        $attempt = StudentExamAttempt::where('exam_id', $exam->id)
                                   ->where('student_id', $student->id)
                                   ->where('status', 'started')
                                   ->firstOrFail();

        // Check if time is up
        $timeRemaining = $this->calculateTimeRemaining($attempt, $exam);
        if ($timeRemaining <= 0) {
            $this->autoSubmitExam($attempt);
            return redirect()->route('student.exams.result', $exam->id)
                           ->with('info', 'Time is up! Your exam has been automatically submitted.');
        }

        // Get student's answers
        $answers = StudentAnswer::where('attempt_id', $attempt->id)
                               ->get()
                               ->keyBy('question_id');

        // Randomize questions if enabled
        $questions = $exam->questions()->active()->orderByNumber()->get();
        if ($exam->randomize_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.exams.take', compact('exam', 'attempt', 'questions', 'answers', 'timeRemaining'));
    }

    /**
     * Save answer for a question
     */
    public function saveAnswer(Request $request, $examId, $questionId)
    {
        $student = students::where('user_id', Auth::id())->firstOrFail();
        $exam = Exam::findOrFail($examId);
        $question = ExamQuestion::where('exam_id', $examId)->findOrFail($questionId);

        // Get active attempt
        $attempt = StudentExamAttempt::where('exam_id', $exam->id)
                                   ->where('student_id', $student->id)
                                   ->where('status', 'started')
                                   ->firstOrFail();

        // Check if time is up
        $timeRemaining = $this->calculateTimeRemaining($attempt, $exam);
        if ($timeRemaining <= 0) {
            return response()->json(['error' => 'Time is up!'], 400);
        }

        $validator = Validator::make($request->all(), [
            'selected_option' => 'nullable|in:A,B,C,D,E',
            'answer_text' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            // Find or create answer
            $answer = StudentAnswer::where('attempt_id', $attempt->id)
                                 ->where('question_id', $question->id)
                                 ->first();

            if (!$answer) {
                $answer = new StudentAnswer([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'question_type' => $question->question_type
                ]);
            }

            // Update answer based on question type
            if ($question->question_type === 'mcq') {
                $answer->selected_option = $request->selected_option;
            } else {
                $answer->answer_text = $request->answer_text;
            }

            $answer->answered_at = now();
            $answer->save();

            // Auto-grade MCQ questions
            if ($question->question_type === 'mcq' && $answer->selected_option) {
                $answer->autoGradeMcq();
            }

            // Update attempt statistics
            $this->updateAttemptStatistics($attempt);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Answer saved successfully',
                'time_remaining' => $this->calculateTimeRemaining($attempt, $exam)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to save answer'], 500);
        }
    }

    /**
     * Submit exam
     */
    public function submitExam($id)
    {
        $student = students::where('user_id', Auth::id())->firstOrFail();
        $exam = Exam::findOrFail($id);

        // Get active attempt
        $attempt = StudentExamAttempt::where('exam_id', $exam->id)
                                   ->where('student_id', $student->id)
                                   ->where('status', 'started')
                                   ->firstOrFail();

        DB::beginTransaction();
        try {
            $this->finalizeExamSubmission($attempt, 'submitted');

            DB::commit();
            return redirect()->route('student.exams.result', $exam->id)
                           ->with('success', 'Exam submitted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to submit exam. Please try again.');
        }
    }

    /**
     * View exam result
     */
    public function viewResult($id)
    {
        $student = students::where('user_id', Auth::id())->firstOrFail();
        $exam = Exam::with(['examType', 'subject'])->findOrFail($id);

        $attempt = StudentExamAttempt::where('exam_id', $exam->id)
                                   ->where('student_id', $student->id)
                                   ->whereIn('status', ['submitted', 'auto_submitted', 'graded'])
                                   ->firstOrFail();

        $result = ExamResult::where('exam_id', $exam->id)
                          ->where('student_id', $student->id)
                          ->first();

        // Get detailed answers if results are shown
        $answers = null;
        if ($exam->show_results) {
            $answers = StudentAnswer::with(['question.mcqOptions'])
                                  ->where('attempt_id', $attempt->id)
                                  ->get()
                                  ->keyBy('question_id');
        }

        return view('student.exams.result', compact('exam', 'attempt', 'result', 'answers'));
    }

    /**
     * Get current exam time remaining (AJAX)
     */
    public function getTimeRemaining($id)
    {
        $student = students::where('user_id', Auth::id())->firstOrFail();
        $exam = Exam::findOrFail($id);

        $attempt = StudentExamAttempt::where('exam_id', $exam->id)
                                   ->where('student_id', $student->id)
                                   ->where('status', 'started')
                                   ->first();

        if (!$attempt) {
            return response()->json(['error' => 'No active attempt found'], 404);
        }

        $timeRemaining = $this->calculateTimeRemaining($attempt, $exam);

        // Auto-submit if time is up
        if ($timeRemaining <= 0) {
            $this->autoSubmitExam($attempt);
            return response()->json(['time_up' => true]);
        }

        return response()->json(['time_remaining' => $timeRemaining]);
    }

    /**
     * Helper methods
     */
    private function isExamAvailable($exam)
    {
        $now = now();
        $examDateTime = Carbon::parse($exam->exam_date->format('Y-m-d') . ' ' . $exam->exam_time->format('H:i:s'));
        $examEndTime = $examDateTime->copy()->addMinutes($exam->duration_minutes + 10); // 10 minutes buffer

        return $now >= $examDateTime && $now <= $examEndTime;
    }

    private function calculateTimeRemaining($attempt, $exam)
    {
        $endTime = $attempt->start_time->copy()->addMinutes($exam->duration_minutes);
        return max(0, now()->diffInMinutes($endTime, false));
    }

    private function updateAttemptStatistics($attempt)
    {
        $answers = StudentAnswer::where('attempt_id', $attempt->id)->get();
        
        $attempt->attempted_questions = $answers->count();
        $attempt->correct_answers = $answers->where('is_correct', true)->count();
        $attempt->wrong_answers = $answers->where('is_correct', false)->count();
        $attempt->total_marks_obtained = $answers->sum('marks_obtained');
        
        if ($attempt->exam->total_marks > 0) {
            $attempt->percentage = ($attempt->total_marks_obtained / $attempt->exam->total_marks) * 100;
        }
        
        $attempt->grade = $attempt->calculateGrade();
        $attempt->save();
    }

    private function autoSubmitExam($attempt)
    {
        $this->finalizeExamSubmission($attempt, 'auto_submitted');
    }

    private function finalizeExamSubmission($attempt, $status)
    {
        $attempt->end_time = now();
        $attempt->duration_taken = $attempt->start_time->diffInMinutes($attempt->end_time);
        $attempt->status = $status;
        
        // Update final statistics
        $this->updateAttemptStatistics($attempt);
        $attempt->save();

        // Create exam result
        $result = ExamResult::updateOrCreate(
            [
                'exam_id' => $attempt->exam_id,
                'student_id' => $attempt->student_id
            ],
            [
                'attempt_id' => $attempt->id,
                'total_marks' => $attempt->exam->total_marks,
                'obtained_marks' => $attempt->total_marks_obtained,
                'percentage' => $attempt->percentage,
                'grade' => $attempt->grade,
                'status' => $attempt->percentage >= $attempt->exam->passing_percentage ? 'pass' : 'fail',
                'remarks' => null // Will be generated later
            ]
        );

        // Calculate position among all students
        $result->calculatePosition();
    }
}
