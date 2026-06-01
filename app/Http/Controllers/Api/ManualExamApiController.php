<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManualExamApiController extends Controller
{
    public function getDependencies()
    {
        $classes = DB::table('classes')->orderBy('className')->select('id', 'className')->get();

        // Fetch subjects with class_id for dependent dropdown
        $subjects = DB::table('subjects')
            ->orderBy('subject_name')
            ->select('id', 'subject_name as title', 'subject_code as code', 'class_id')
            ->where('status', 'active') // Assuming active status check is good practice
            ->get();

        // Build Class -> Sections Map
        // Join students with classes to map PRESENT_CLASS string to class ID
        $rawSections = DB::table('students')
            ->join('classes', 'students.PRESENT_CLASS', '=', 'classes.className')
            ->select('classes.id as class_id', 'students.SECTION')
            ->whereNotNull('students.SECTION')
            ->distinct()
            ->orderBy('students.SECTION')
            ->get();

        $classSections = [];
        foreach ($rawSections as $row) {
            $classSections[$row->class_id][] = $row->SECTION;
        }

        // Get basic terms
        $terms = ['First Term', 'Mid Term', 'Final Term']; // Can acturaly query exam_terms table if needed

        return response()->json([
            'classes' => $classes,
            'subjects' => $subjects,
            'class_sections' => $classSections,
            'terms' => $terms
        ]);
    }

    public function getStudents(Request $request)
    {
        $classId = $request->input('class_id');
        $section = $request->input('section');
        $term = $request->input('term');
        $subjectId = $request->input('subject_id');

        // Find class name for filtering students
        $className = DB::table('classes')->where('id', $classId)->value('className');

        $query = DB::table('students')
            ->where('PRESENT_CLASS', $className)
            ->select('GRNO as id', 'GRNO', 'NAME', 'F_NAME as FNAME', 'SECTION', 'PRESENT_CLASS');

        if ($section) {
            $query->where('SECTION', $section);
        }

        // Fix sorting: GRNO ascending
        $students = $query->orderByRaw('CAST(GRNO AS UNSIGNED) ASC')->get();

        // Fetch existing marks
        // The legacy system stores all marks in a single row per student/term with subject='all'
        // and a JSON 'data' column keyed by the slugified subject name.

        $subjectSlug = null;
        if ($subjectId) {
            $subjectName = DB::table('subjects')->where('id', $subjectId)->value('subject_name');
            if ($subjectName) {
                // Determine format based on ManualExamsController logic
                // $key = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $label));
                $subjectSlug = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subjectName));
            }
        }

        if ($subjectSlug && \Illuminate\Support\Facades\Schema::hasTable('manual_exam_entries')) {
            // Fetch all entries for this class/term
            // We do NOT use session as it appears missing from the unique key in the legacy schema
            $entries = DB::table('manual_exam_entries')
                ->where('class_id', $classId)
                ->where('term', $term)
                ->where('subject', 'all') // The legacy controller uses 'all'
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $mark = null;
                // Student ID in manual_exam_entries is the GRNO
                // In our student query above, 'id' is aliased to GRNO, so we can use $student->id
                if (isset($entries[$student->id])) {
                    $entry = $entries[$student->id];
                    $jsonData = json_decode($entry->data, true);
                    if (is_array($jsonData) && isset($jsonData[$subjectSlug])) {
                        $mark = $jsonData[$subjectSlug];
                    }
                }
                $student->marks = $mark;
            }
        } else {
            foreach ($students as $student) {
                $student->marks = null;
            }
        }

        return response()->json([
            'students' => $students
        ]);
    }

    public function saveMarks(Request $request)
    {
        $entries = $request->input('entries'); // Array of {student_id (GRNO), subject_id, marks_obtained, ...}
        $term = $request->input('term');
        $classId = (int) $request->input('class_id');
        $section = $request->input('section');

        // We need the subject slug
        if (empty($entries)) {
            return response()->json(['status' => 'success', 'message' => 'No entries']);
        }

        // Assuming all entries are for the same subject, pick the first one
        $first = $entries[0];
        $subjectId = $first['subject_id'];
        $subjectName = DB::table('subjects')->where('id', $subjectId)->value('subject_name');
        if (!$subjectName) {
            return response()->json(['status' => 'error', 'message' => 'Subject not found'], 404);
        }
        $subjectSlug = strtolower(str_replace([' ', '(', ')', '.', '&', '-'], ['_', '', '', '', '', '_'], $subjectName));

        DB::beginTransaction();
        try {
            foreach ($entries as $entry) {
                $studentId = $entry['student_id']; // This is GRNO
                $marks = $entry['marks_obtained'];

                // 1. Fetch existing row
                $row = DB::table('manual_exam_entries')
                    ->where('class_id', $classId)
                    ->where('term', $term)
                    ->where('student_id', $studentId)
                    ->where('subject', 'all')
                    ->lockForUpdate()
                    ->first();

                // 2. Prepare JSON data
                $data = [];
                if ($row) {
                    $json = json_decode($row->data, true);
                    if (is_array($json)) {
                        $data = $json;
                    }
                }

                // 3. Update the specific subject mark
                $data[$subjectSlug] = $marks;

                // 4. Update or Insert
                DB::table('manual_exam_entries')->updateOrInsert(
                    [
                        'class_id' => $classId,
                        'student_id' => $studentId,
                        'term' => $term,
                        'subject' => 'all'
                    ],
                    [
                        'data' => json_encode($data),
                        'updated_at' => now(),
                        // 'created_at' is handled by Eloquent usually but query builder might need manual set if insert. 
                        // updateOrInsert doesn't verify created_at on update.
                    ]
                );
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Marks saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SaveMarks Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save marks: ' . $e->getMessage()], 500);
        }
    }
}
