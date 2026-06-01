<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\students;
use App\Models\Exam;
use App\Models\StudentExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:student']);
    }

    /**
     * Show student dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'Student profile not found.');
        }

        // Get available exams for the student
        $availableExams = Exam::where('status', 'published')
                             ->where('class_id', $student->class_id)
                             ->where('exam_date', '>=', now())
                             ->with(['subject', 'examType'])
                             ->limit(5)
                             ->get();

        // Get student's exam attempts
        $examAttempts = StudentExamAttempt::where('student_id', $student->id)
                                        ->with(['exam.subject', 'exam.examType'])
                                        ->latest()
                                        ->limit(10)
                                        ->get();

        // Get upcoming exams (next 7 days)
        $upcomingExams = Exam::where('status', 'published')
                           ->where('class_id', $student->class_id)
                           ->whereBetween('exam_date', [now(), now()->addDays(7)])
                           ->with(['subject', 'examType'])
                           ->get();

        // Calculate stats
        $totalExams = Exam::where('class_id', $student->class_id)->where('status', 'published')->count();
        $completedExams = $examAttempts->where('status', 'completed')->count();
        $averageScore = $examAttempts->where('percentage', '>', 0)->avg('percentage') ?? 0;

        return view('student.dashboard', compact(
            'student',
            'availableExams',
            'examAttempts',
            'upcomingExams',
            'totalExams',
            'completedExams',
            'averageScore'
        ));
    }

    /**
     * Show available exams for student
     */
    public function exams()
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'Student profile not found.');
        }

        $exams = Exam::where('status', 'published')
                    ->where('class_id', $student->class_id)
                    ->with(['subject', 'examType', 'attempts' => function($query) use ($student) {
                        $query->where('student_id', $student->id);
                    }])
                    ->paginate(15);

        return view('student.exams', compact('student', 'exams'));
    }

    /**
     * Show exam results for student
     */
    public function results()
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'Student profile not found.');
        }

        $results = StudentExamAttempt::where('student_id', $student->id)
                                   ->whereIn('status', ['completed', 'graded'])
                                   ->with(['exam.subject', 'exam.examType'])
                                   ->latest()
                                   ->paginate(15);

        return view('student.results', compact('student', 'results'));
    }

    /**
     * Show student profile
     */
    public function profile()
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'Student profile not found.');
        }

        return view('student.profile', compact('student'));
    }

    /**
     * Generate student login credentials
     */
    public static function generateStudentCredentials()
    {
        $students = students::with('user')->get();
        $credentials = [];

        foreach ($students as $student) {
            if (!$student->user) {
                // Create user account for student if doesn't exist
                $user = User::create([
                    'name' => $student->studentName,
                    'email' => 'student' . $student->grno . '@school.edu',
                    'password' => Hash::make('pass_' . $student->grno),
                    'role' => 'student',
                    'school_id' => $student->school_id,
                ]);

                // Update student record with user_id
                $student->update(['user_id' => $user->id]);

                $credentials[] = [
                    'name' => $student->studentName,
                    'roll_number' => $student->grno,
                    'username' => 'student' . $student->grno . '@school.edu',
                    'password' => 'pass_' . $student->grno,
                    'class' => $student->class ? $student->class->className : 'N/A',
                ];
            } else {
                $credentials[] = [
                    'name' => $student->studentName,
                    'roll_number' => $student->grno,
                    'username' => $student->user->email,
                    'password' => 'pass_' . $student->grno . ' (if not changed)',
                    'class' => $student->class ? $student->class->className : 'N/A',
                ];
            }
        }

        return $credentials;
    }
}