<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ExamType;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\StudentExamAttempt;
use App\Models\ExamResult;

class StudentsResultsSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // Resolve active session (academic year)
            $activeYear = DB::table('academicyears')->where('is_active', 'yes')->first();
            $session = $activeYear->academicYear ?? (date('Y') . '-' . (date('Y') + 1));

            // Ensure core exam types exist
            $midType = ExamType::firstOrCreate(
                ['exam_type_name' => 'Mid Term Exam'],
                ['school_id' => 1, 'description' => 'Mid semester examination', 'status' => 'active']
            );
            $finalType = ExamType::firstOrCreate(
                ['exam_type_name' => 'Final Exam'],
                ['school_id' => 1, 'description' => 'Final semester examination', 'status' => 'active']
            );

            // Prefer Mathematics and English subjects; fallback to any
            $math = Subject::where('subject_name', 'Mathematics')->first();
            $eng = Subject::where('subject_name', 'English')->first();
            if (!$math) { $math = Subject::first(); }
            if (!$eng)  { $eng = Subject::first(); }

            // Iterate all classes
            $classes = DB::table('classes')->select('id','className','school_id')->get();
            $createdExams = 0; $createdResults = 0;

            foreach ($classes as $cls) {
                // Choose any teacher id if available for metadata
                $teacherId = DB::table('teachers')->where('class_id', $cls->id)->value('id');

                // Build two exams per class: Mid (English) and Final (Math)
                $examsToEnsure = [
                    [
                        'type' => $midType,
                        'subject' => $eng,
                        'name' => sprintf('Mid Term - %s - %s - %s', $cls->className, $session, $eng?->subject_name ?? 'General'),
                        'date_shift' => 7,
                    ],
                    [
                        'type' => $finalType,
                        'subject' => $math,
                        'name' => sprintf('Final Exam - %s - %s - %s', $cls->className, $session, $math?->subject_name ?? 'General'),
                        'date_shift' => 30,
                    ],
                ];

                foreach ($examsToEnsure as $def) {
                    if (!$def['subject']) { continue; }
                    $exam = Exam::firstOrCreate(
                        ['exam_name' => $def['name']],
                        [
                            'school_id' => $cls->school_id ?? 1,
                            'session' => $session,
                            'exam_type_id' => $def['type']->id,
                            'class_id' => (string)$cls->id,
                            'class_name' => $cls->className,
                            'subject_id' => $def['subject']->id,
                            'teacher_id' => $teacherId,
                            'exam_date' => now()->addDays($def['date_shift'])->toDateString(),
                            'exam_time' => now()->setTime(10, 0),
                            'duration_minutes' => 90,
                            'total_marks' => 100,
                            'passing_marks' => 33,
                            'total_questions' => 0,
                            'mcq_questions' => 0,
                            'short_questions' => 0,
                            'long_questions' => 0,
                            'instructions' => 'Auto-generated exam for seeding results.',
                            'status' => 'published',
                            'auto_submit' => true,
                            'show_results' => true,
                            'randomize_questions' => false,
                            'created_by' => 1,
                        ]
                    );
                    if ($exam->wasRecentlyCreated) { $createdExams++; }

                    // Pick up to 5 active students from this class
                    $students = DB::table('students')
                        ->where('class_id', $cls->id)
                        ->whereIn('status', ['active', 'Active'])
                        ->orderBy('id')
                        ->limit(5)
                        ->get();

                    foreach ($students as $st) {
                        // Create/ensure an attempt (use start_time/end_time per migration)
                        $attempt = StudentExamAttempt::updateOrCreate(
                            [
                                'exam_id' => $exam->id,
                                'student_id' => $st->id,
                            ],
                            [
                                'attempt_number' => 1,
                                'start_time' => now()->subHours(2),
                                'end_time' => now()->subHours(1),
                                'duration_taken' => 60,
                                'total_questions' => 0,
                                'attempted_questions' => 0,
                                'correct_answers' => 0,
                                'wrong_answers' => 0,
                                'total_marks_obtained' => 0,
                                'percentage' => 0,
                                'grade' => null,
                                'status' => 'submitted',
                                'ip_address' => '127.0.0.1',
                                'browser_info' => 'Seeder',
                            ]
                        );

                        // Random but sensible marks
                        $obtained = rand(35, 95); // 35-95 out of 100
                        $percentage = $obtained; // because total is 100
                        $grade = $this->gradeFromPercentage($percentage);
                        $status = $obtained >= 33 ? 'pass' : 'fail';

                        $res = ExamResult::updateOrCreate(
                            [
                                'exam_id' => $exam->id,
                                'student_id' => $st->id,
                            ],
                            [
                                'attempt_id' => $attempt->id,
                                'obtained_marks' => $obtained,
                                'total_marks' => 100,
                                'percentage' => $percentage,
                                'grade' => $grade,
                                'status' => $status,
                                'remarks' => $status === 'pass' ? 'Good performance' : 'Needs improvement',
                            ]
                        );
                        if ($res->wasRecentlyCreated) { $createdResults++; }
                    }
                }
            }

            DB::commit();
            $this->command?->info("StudentsResultsSeeder completed. Exams created: {$createdExams}, Results created: {$createdResults}");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command?->error('StudentsResultsSeeder failed: ' . $e->getMessage());
        }
    }

    private function gradeFromPercentage(float $p): string
    {
        if ($p >= 90) return 'A+';
        if ($p >= 80) return 'A';
        if ($p >= 70) return 'B+';
        if ($p >= 60) return 'B';
        if ($p >= 50) return 'C+';
        if ($p >= 40) return 'C';
        return 'F';
    }
}
