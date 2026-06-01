<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ExamType;
use App\Models\Subject;
use App\Models\Classes;

class ExamDataSeeder extends Seeder
{
    public function run()
    {
        // Create Exam Types
        $examTypes = [
            [
                'school_id' => 1,
                'exam_type_name' => 'Monthly Test',
                'description' => 'Regular monthly examinations',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'school_id' => 1,
                'exam_type_name' => 'Mid Term Exam',
                'description' => 'Mid semester examinations',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'school_id' => 1,
                'exam_type_name' => 'Final Term Exam',
                'description' => 'End of semester examinations',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'school_id' => 1,
                'exam_type_name' => 'Quiz',
                'description' => 'Quick assessment tests',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'school_id' => 1,
                'exam_type_name' => 'Assignment Test',
                'description' => 'Take-home assignments',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('exam_types')->insert($examTypes);

        // Get all classes
        $classes = Classes::all();
        
        // Always define $subjects to avoid undefined variable notices
        $subjects = [];
        
        if ($classes->count() > 0) {
            
            // Common subjects for all classes
            $commonSubjects = [
                ['name' => 'English', 'code' => 'ENG', 'marks' => 100, 'passing' => 33],
                ['name' => 'Mathematics', 'code' => 'MATH', 'marks' => 100, 'passing' => 33],
                ['name' => 'Science', 'code' => 'SCI', 'marks' => 100, 'passing' => 33],
                ['name' => 'Urdu', 'code' => 'URD', 'marks' => 100, 'passing' => 33],
                ['name' => 'Islamic Studies', 'code' => 'ISL', 'marks' => 50, 'passing' => 17],
                ['name' => 'Social Studies', 'code' => 'SS', 'marks' => 75, 'passing' => 25],
            ];

            foreach ($classes as $class) {
                $sortOrder = 1;
                foreach ($commonSubjects as $subject) {
                    $subjects[] = [
                        'subject_name' => $subject['name'],
                        'subject_code' => $subject['code'] . '_C' . $class->id, // Make unique per class
                        'class_id' => $class->id,
                        'total_marks' => $subject['marks'],
                        'passing_marks' => $subject['passing'],
                        'status' => 'active',
                        'sort_order' => $sortOrder++,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            DB::table('subjects')->insert($subjects);
        }

        echo "✅ Created " . count($examTypes) . " exam types\n";
        echo "✅ Created " . count($subjects) . " subjects for " . $classes->count() . " classes\n";
    }
}