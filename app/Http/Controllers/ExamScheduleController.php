<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\teachers;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ExamScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the exam schedule index page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get exams based on user role
        $examQuery = Exam::with(['subject', 'examType', 'teacher']);
        
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            if ($teacher) {
                $examQuery->where('teacher_id', $teacher->id);
            }
        } elseif ($user->role === 'admin') {
            // Admin can see all exams for their school
            $examQuery->where('school_id', 1);
        }
        // Superadmin can see all exams
        
        $scheduledExams = $examQuery->whereNotNull('exam_date')
                                   ->where('status', '!=', 'archived')
                                   ->orderBy('exam_date', 'asc')
                                   ->get();
        
        // Get available exams that can be scheduled (draft status)
        $availableExams = Exam::with(['subject', 'examType'])
                               ->where('status', 'draft')
                               ->get();
        
        // Filter by user role if needed
        if ($user->role === 'teacher') {
            $teacher = teachers::where('user_id', $user->id)->first();
            if ($teacher) {
                $availableExams = $availableExams->where('teacher_id', $teacher->id);
            }
        }
        
        // Get upcoming exams (next 7 days)
        $upcomingExams = $scheduledExams->filter(function ($exam) {
            return Carbon::parse($exam->exam_date)->between(now(), now()->addDays(7));
        });
        
        // Get statistics
        $todayExams = $scheduledExams->filter(function ($exam) {
            return Carbon::parse($exam->exam_date)->isToday();
        })->count();
        
        $thisWeekExams = $scheduledExams->filter(function ($exam) {
            return Carbon::parse($exam->exam_date)->between(now()->startOfWeek(), now()->endOfWeek());
        })->count();
        
        $thisMonthExams = $scheduledExams->filter(function ($exam) {
            return Carbon::parse($exam->exam_date)->between(now()->startOfMonth(), now()->endOfMonth());
        })->count();
        
        // Get all subjects, teachers, and classes for dropdowns
        $subjects = Subject::active()->get();
        $teachers = teachers::all();
        $classes = Classes::active()->get();
        
        return view('exam-schedule.index', compact(
            'scheduledExams',
            'availableExams', 
            'upcomingExams',
            'todayExams',
            'thisWeekExams',
            'thisMonthExams',
            'subjects',
            'teachers',
            'classes'
        ));
    }

    /**
     * Store a new exam schedule
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exams,id',
            'exam_date' => 'required|date|after_or_equal:today',
            'exam_time' => 'required',
            'class_id' => 'required|exists:classes,id',
            'room_number' => 'nullable|string|max:20',
            'invigilator_id' => 'nullable|exists:teachers,id',
            'special_instructions' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            $exam = Exam::findOrFail($request->exam_id);
            $class = Classes::findOrFail($request->class_id);
            
            // Update exam with schedule details
            $exam->update([
                'exam_date' => $request->exam_date,
                'exam_time' => $request->exam_time,
                'class_id' => $request->class_id,
                'class_name' => $class->className,
                'status' => 'published',
                'room_number' => $request->room_number,
                'invigilator_id' => $request->invigilator_id,
                'instructions' => $request->special_instructions
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Exam scheduled successfully!']);
            }
            
            return redirect()->route('exam-schedule.index')
                           ->with('success', 'Exam scheduled successfully!');
                           
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to schedule exam'], 500);
            }
            
            return redirect()->back()
                           ->with('error', 'Failed to schedule exam')
                           ->withInput();
        }
    }

    /**
     * Update an exam schedule
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'exam_date' => 'required|date|after_or_equal:today',
            'exam_time' => 'required',
            'room_number' => 'nullable|string|max:20',
            'invigilator_id' => 'nullable|exists:teachers,id',
            'special_instructions' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            $exam = Exam::findOrFail($id);
            
            $exam->update([
                'exam_date' => $request->exam_date,
                'exam_time' => $request->exam_time,
                'room_number' => $request->room_number,
                'invigilator_id' => $request->invigilator_id,
                'instructions' => $request->special_instructions
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Schedule updated successfully!']);
            }
            
            return redirect()->route('exam-schedule.index')
                           ->with('success', 'Schedule updated successfully!');
                           
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to update schedule'], 500);
            }
            
            return redirect()->back()
                           ->with('error', 'Failed to update schedule')
                           ->withInput();
        }
    }

    /**
     * Cancel/delete an exam schedule
     */
    public function destroy($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            
            // Set exam back to draft and remove schedule
            $exam->update([
                'exam_date' => null,
                'exam_time' => null,
                'status' => 'draft'
            ]);
            
            return response()->json(['success' => true, 'message' => 'Exam schedule cancelled successfully!']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to cancel exam'], 500);
        }
    }

    /**
     * Display the specified exam schedule
     */
    public function show($id)
    {
        $exam = Exam::with(['subject', 'examType', 'teacher'])->findOrFail($id);
        
        // Structure the response to match what the JS expects (or simpler, just return exam and fix JS)
        // Returning the exam directly is cleaner.
        if (request()->ajax()) {
            return response()->json($exam);
        }
        
        return view('exam-schedule.show', compact('exam'));
    }

    /**
     * Display calendar view
     */
    public function calendar()
    {
        return $this->index();
    }
}
