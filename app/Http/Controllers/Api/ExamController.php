<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function getExams()
    {
        return response()->json(Exam::with(['subject', 'teacher'])->get());
    }

    public function storeExam(Request $request)
    {
        $data = $request->validate([
            'exam_name' => 'required',
            'subject_id' => 'required',
            'exam_date' => 'required|date',
            'total_marks' => 'required|integer',
            'passing_marks' => 'required|integer',
        ]);
        $data['school_id'] = 2;
        $data['session'] = 'April 2024 to March 2025';
        $data['status'] = 'published';

        $exam = Exam::create($data);
        return response()->json($exam, 201);
    }

    public function getResults()
    {
        return response()->json(ExamResult::with(['student', 'exam'])->get());
    }
}
