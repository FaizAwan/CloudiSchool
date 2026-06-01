<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\QuestionBankOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of questions in the bank
     */
    public function index(Request $request)
    {
        $query = QuestionBank::with(['subject', 'creator']);
        
        // Apply filters (accept both legacy UI params and canonical names)
        $subjectId = $request->get('subject_id', $request->get('subject'));
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        $classLevel = $request->get('class_level', $request->get('class'));
        if ($classLevel) {
            $query->where('class_level', $classLevel);
        }
        
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }
        
        $questionType = $request->get('question_type', $request->get('type'));
        if ($questionType) {
            $query->where('question_type', $questionType);
        }
        
        if ($request->filled('search')) {
            $query->where('question_text', 'LIKE', '%' . $request->search . '%');
        }
        
        $questionBankItems = $query->latest()->paginate(15);
        $subjects = Subject::active()->get();
        $classes = Classes::all();
        
        // Get available exams for "add to exam" functionality
        $user = Auth::user();
        $examQuery = \App\Models\Exam::with(['subject']);
        
        if ($user->role === 'teacher') {
            $teacher = \App\Models\teachers::where('user_id', $user->id)->first();
            if ($teacher) {
                $examQuery->where('teacher_id', $teacher->id);
            }
        } elseif ($user->role === 'admin') {
            // Admin can see all exams for their school
            $examQuery->where('school_id', 1);
        }
        // Superadmin can see all exams (no additional filter)
        
        $availableExams = $examQuery->where('status', 'draft')->get();
        
        return view('question-bank.index', compact('questionBankItems', 'subjects', 'classes', 'availableExams'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create()
    {
        $subjects = Subject::active()->get();
        $classes = Classes::all();
        
        return view('question-bank.create', compact('subjects', 'classes'));
    }

    /**
     * Store a newly created question in the bank
     */
    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Question Bank Form Data:', $request->all());
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'class_level' => 'required|string',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,short,long,true_false,fill_blank',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'marks' => 'required|integer|min:1|max:100',
            'explanation' => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'topic' => 'nullable|string',
            'chapter' => 'nullable|string',
            'options' => 'required_if:question_type,mcq|array|min:2|max:6',
            'options.*' => 'required_if:question_type,mcq|string',
            'correct_option' => 'required_if:question_type,mcq|integer|min:0'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        DB::beginTransaction();
        try {
            $questionData = $request->except(['options', 'correct_option']);
            $questionData['school_id'] = 1;
            $questionData['created_by'] = Auth::id();
            $questionData['status'] = 'active';
            $questionData['usage_count'] = 0;
            
            $question = QuestionBank::create($questionData);
            
            // Handle MCQ options
            if ($request->question_type === 'mcq' && $request->has('options')) {
                $letters = ['A', 'B', 'C', 'D', 'E'];
                foreach ($request->options as $index => $optionText) {
                    if (!empty($optionText)) {
                        QuestionBankOption::create([
                            'question_id' => $question->id,
                            'option_letter' => $letters[$index] ?? 'A',
                            'option_text' => $optionText,
                            'is_correct' => ($index == $request->correct_option)
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Question added to bank successfully!']);
            }
            return redirect()->route('question-bank.index')
                           ->with('success', 'Question added to bank successfully!');
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to add question. Please try again.'], 500);
            }
            return redirect()->back()
                           ->with('error', 'Failed to add question. Please try again.')
                           ->withInput();
        }
    }

    /**
     * Display the specified question
     */
    public function show(Request $request, $id)
    {
        $question = QuestionBank::with(['subject', 'creator', 'mcqOptions'])->findOrFail($id);
        
        // If AJAX request, return JSON data for modal
        if ($request->ajax()) {
            return response()->json($question->load(['subject', 'creator', 'mcqOptions']));
        }
        
        // Otherwise return the full page view
        return view('question-bank.show', compact('question'));
    }

    /**
     * Show the form for editing the specified question
     */
    public function edit($id)
    {
        $question = QuestionBank::with('mcqOptions')->findOrFail($id);
        $subjects = Subject::active()->get();
        $classes = Classes::all();
        
        return view('question-bank.edit', compact('question', 'subjects', 'classes'));
    }

    /**
     * Update the specified question in the bank
     */
    public function update(Request $request, $id)
    {
        $question = QuestionBank::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'class_level' => 'required|string',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,short,long,true_false,fill_blank',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'marks' => 'required|integer|min:1|max:100',
            'explanation' => 'nullable|string',
            'tags' => 'nullable|string',
            'options' => 'required_if:question_type,mcq|array|min:2|max:6',
            'options.*' => 'required_if:question_type,mcq|string',
            'correct_option' => 'required_if:question_type,mcq|integer|min:0'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        DB::beginTransaction();
        try {
            $questionData = $request->except(['options', 'correct_option']);
            
            if ($request->filled('tags')) {
                $questionData['tags'] = array_map('trim', explode(',', $request->tags));
            }
            
            $question->update($questionData);
            
            // Update MCQ options
            if ($request->question_type === 'mcq') {
                // Delete existing options
                QuestionBankOption::where('question_id', $question->id)->delete();
                
                // Add new options
                if ($request->has('options')) {
                    $letters = ['A', 'B', 'C', 'D', 'E'];
                    foreach ($request->options as $index => $optionText) {
                        if (!empty($optionText)) {
                            QuestionBankOption::create([
                                'question_id' => $question->id,
                                'option_letter' => $letters[$index] ?? 'A',
                                'option_text' => $optionText,
                                'is_correct' => ($index == $request->correct_option)
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('question-bank.index')
                           ->with('success', 'Question updated successfully!');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to update question. Please try again.')
                           ->withInput();
        }
    }

    /**
     * Remove the specified question from the bank
     */
    public function destroy($id)
    {
        try {
            $question = QuestionBank::findOrFail($id);
            
            DB::beginTransaction();
            
            // Delete related options
            QuestionBankOption::where('question_id', $question->id)->delete();
            
            // Delete the question
            $question->delete();
            
            DB::commit();
            return redirect()->route('question-bank.index')
                           ->with('success', 'Question deleted successfully!');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('question-bank.index')
                           ->with('error', 'Failed to delete question. It may be used in exams.');
        }
    }

    /**
     * Get questions by subject for AJAX calls
     */
    public function getBySubject($subjectId)
    {
        $questions = QuestionBank::where('subject_id', $subjectId)
                                 ->active()
                                 ->with('mcqOptions')
                                 ->get();
        
        return response()->json($questions);
    }
    
    /**
     * Add a question from bank to an exam
     */
    public function addToExam(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Add to Exam Request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'question_bank_id' => 'required|exists:question_bank,id',
            'exam_id' => 'required|exists:exams,id',
            'marks' => 'required|integer|min:1|max:100'
        ]);
        
        if ($validator->fails()) {
            \Log::warning('Add to Exam Validation Failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            $bankQuestion = QuestionBank::with('mcqOptions')->findOrFail($request->question_bank_id);
            $exam = \App\Models\Exam::findOrFail($request->exam_id);
            
            // Check if question already exists in exam
            $existingQuestion = \App\Models\ExamQuestion::where('exam_id', $exam->id)
                                                        ->where('question_bank_id', $bankQuestion->id)
                                                        ->first();
            
            if ($existingQuestion) {
                return response()->json(['error' => 'Question already exists in this exam'], 400);
            }
            
            DB::beginTransaction();
            
            // Create exam question from bank question
            $examQuestion = \App\Models\ExamQuestion::create([
                'exam_id' => $exam->id,
                'question_bank_id' => $bankQuestion->id,
                'question_text' => $bankQuestion->question_text,
                'question_type' => $bankQuestion->question_type,
                'marks' => $request->marks,
                'difficulty_level' => $bankQuestion->difficulty_level,
                'question_number' => $exam->questions()->count() + 1,
                'status' => 'active'
            ]);
            
            // Copy MCQ options if applicable
            if ($bankQuestion->question_type === 'mcq' && $bankQuestion->mcqOptions) {
                $letters = ['A', 'B', 'C', 'D', 'E'];
                foreach ($bankQuestion->mcqOptions as $index => $option) {
                    \App\Models\McqOption::create([
                        'question_id' => $examQuestion->id,
                        'option_letter' => $letters[$index] ?? 'A',
                        'option_text' => $option->option_text,
                        'is_correct' => $option->is_correct
                    ]);
                }
            }
            
            // Update exam totals
            $exam->increment('total_questions');
            $exam->increment('total_marks', $request->marks);
            
            // Increment usage count in bank
            $bankQuestion->increment('usage_count');
            
            DB::commit();
            
            \Log::info('Question added to exam successfully', ['exam_id' => $exam->id, 'question_id' => $bankQuestion->id]);
            return response()->json(['success' => true, 'message' => 'Question added to exam successfully']);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Failed to add question to exam:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return detailed error in development
            if (config('app.debug')) {
                return response()->json([
                    'error' => 'Database error: ' . $e->getMessage(),
                    'debug' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ], 500);
            }
            
            return response()->json([
                'error' => 'Failed to add question to exam. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Delete multiple questions from bank
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_ids' => 'required|array',
            'question_ids.*' => 'required|exists:question_bank,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $questionIds = $request->question_ids;
            
            // Delete associated options first
            QuestionBankOption::whereIn('question_id', $questionIds)->delete();
            
            // Delete questions
            $deletedCount = QuestionBank::whereIn('id', $questionIds)->delete();
            
            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => "Successfully deleted {$deletedCount} questions from bank"
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to delete questions'], 500);
        }
    }
}
