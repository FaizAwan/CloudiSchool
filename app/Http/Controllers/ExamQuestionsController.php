<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\McqOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExamQuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($examId)
    {
        $exam = Exam::with(['subject','examType','questions.mcqOptions'])->findOrFail($examId);
        $questions = $exam->questions()->orderBy('question_number')->get();
        return view('exams.questions', compact('exam', 'questions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exams,id',
            'question_type' => 'required|in:mcq,short,long,true_false,fill_blank',
            'question_text' => 'required|string',
            'marks' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'options' => 'array',
            'options.*' => 'nullable|string',
            'correct_option' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $exam = Exam::findOrFail($request->exam_id);
            $number = $exam->questions()->count() + 1;

            $question = ExamQuestion::create([
                'exam_id' => $exam->id,
                'question_number' => $number,
                'question_type' => $request->question_type,
                'question_text' => $request->question_text,
                'marks' => (int)$request->marks,
                'difficulty_level' => 'medium',
                'explanation' => $request->correct_answer,
                'status' => 'active'
            ]);

            if ($request->question_type === 'mcq') {
                $opts = $request->input('options', []);
                $correctIndex = (int)$request->input('correct_option', -1);
                $letters = ['A','B','C','D','E','F'];
                foreach ($opts as $i => $text) {
                    if ($text === null || $text === '') { continue; }
                    McqOption::create([
                        'question_id' => $question->id,
                        'option_letter' => $letters[$i] ?? 'A',
                        'option_text' => $text,
                        'is_correct' => $i === $correctIndex,
                    ]);
                }
            }

            $exam->increment('total_questions');
            $exam->increment('total_marks', (int)$request->marks);

            DB::commit();
            return response()->json(['success' => true, 'id' => $question->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to add question'], 500);
        }
    }

    public function show($id)
    {
        $q = ExamQuestion::with(['mcqOptions'])->findOrFail($id);
        $data = [
            'id' => $q->id,
            'exam_id' => $q->exam_id,
            'question_number' => $q->question_number,
            'question_type' => $q->question_type,
            'question_text' => $q->question_text,
            'marks' => $q->marks,
            'difficulty_level' => $q->difficulty_level,
            'correct_answer' => $q->explanation,
            'mcq_options' => $q->isMcq() ? $q->mcqOptions()->orderBy('option_letter')->get(['option_text','is_correct']) : [],
        ];
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question_type' => 'required|in:mcq,short,long,true_false,fill_blank',
            'question_text' => 'required|string',
            'marks' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'options' => 'array',
            'options.*' => 'nullable|string',
            'correct_option' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $q = ExamQuestion::with('exam')->findOrFail($id);
            $deltaMarks = (int)$request->marks - (int)$q->marks;

            $q->update([
                'question_type' => $request->question_type,
                'question_text' => $request->question_text,
                'marks' => (int)$request->marks,
                'explanation' => $request->correct_answer,
            ]);

            if ($request->question_type === 'mcq') {
                McqOption::where('question_id', $q->id)->delete();
                $opts = $request->input('options', []);
                $correctIndex = (int)$request->input('correct_option', -1);
                $letters = ['A','B','C','D','E','F'];
                foreach ($opts as $i => $text) {
                    if ($text === null || $text === '') { continue; }
                    McqOption::create([
                        'question_id' => $q->id,
                        'option_letter' => $letters[$i] ?? 'A',
                        'option_text' => $text,
                        'is_correct' => $i === $correctIndex,
                    ]);
                }
            } else {
                McqOption::where('question_id', $q->id)->delete();
            }

            if ($deltaMarks !== 0) {
                $q->exam->increment('total_marks', $deltaMarks);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update question'], 500);
        }
    }

    public function destroyById($id)
    {
        DB::beginTransaction();
        try {
            $q = ExamQuestion::with('exam')->findOrFail($id);
            $exam = $q->exam;
            $marks = (int)$q->marks;
            McqOption::where('question_id', $q->id)->delete();
            $q->delete();
            $exam->decrement('total_questions');
            $exam->decrement('total_marks', $marks);
            $remaining = $exam->questions()->orderBy('question_number')->get();
            $n = 1;
            foreach ($remaining as $rq) {
                $rq->update(['question_number' => $n++]);
            }
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete question'], 500);
        }
    }

    public function destroy($examId, $questionId)
    {
        return $this->destroyById($questionId);
    }

    public function edit($examId, $questionId)
    {
        $exam = Exam::with(['subject','examType','questions.mcqOptions'])->findOrFail($examId);
        $questions = $exam->questions()->orderBy('question_number')->get();
        return view('exams.questions', compact('exam', 'questions'));
    }
}

