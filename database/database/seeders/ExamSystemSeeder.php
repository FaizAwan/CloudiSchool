<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamType;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\McqOption;
use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use App\Models\Classes;
use App\Models\teachers;
use App\Models\students;
use App\Models\StudentExamAttempt;
use App\Models\ExamResult;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\DB;

class ExamSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Create Exam Types
            $examTypes = [
                ['school_id' => 1, 'exam_type_name' => 'Monthly Test', 'description' => 'Regular monthly assessment', 'status' => 'active'],
                ['school_id' => 1, 'exam_type_name' => 'Mid Term Exam', 'description' => 'Mid semester examination', 'status' => 'active'],
                ['school_id' => 1, 'exam_type_name' => 'Final Exam', 'description' => 'Final semester examination', 'status' => 'active'],
                ['school_id' => 1, 'exam_type_name' => 'Quiz', 'description' => 'Quick assessment quiz', 'status' => 'active'],
                ['school_id' => 1, 'exam_type_name' => 'Unit Test', 'description' => 'Chapter-wise unit test', 'status' => 'active']
            ];

            foreach ($examTypes as $examType) {
                ExamType::firstOrCreate(['exam_type_name' => $examType['exam_type_name']], $examType);
            }

            // Create Subjects
            $subjects = [
                ['school_id' => 1, 'subject_name' => 'Mathematics', 'subject_code' => 'MATH', 'class_level' => 'all', 'total_marks' => 100, 'passing_marks' => 40, 'status' => 'active'],
                ['school_id' => 1, 'subject_name' => 'English', 'subject_code' => 'ENG', 'class_level' => 'all', 'total_marks' => 100, 'passing_marks' => 40, 'status' => 'active'],
                ['school_id' => 1, 'subject_name' => 'Science', 'subject_code' => 'SCI', 'class_level' => 'all', 'total_marks' => 100, 'passing_marks' => 40, 'status' => 'active'],
                ['school_id' => 1, 'subject_name' => 'Social Studies', 'subject_code' => 'SS', 'class_level' => 'all', 'total_marks' => 100, 'passing_marks' => 40, 'status' => 'active'],
                ['school_id' => 1, 'subject_name' => 'Computer Science', 'subject_code' => 'CS', 'class_level' => 'all', 'total_marks' => 100, 'passing_marks' => 40, 'status' => 'active'],
                ['school_id' => 1, 'subject_name' => 'Physics', 'subject_code' => 'PHY', 'class_level' => '11', 'total_marks' => 100, 'passing_marks' => 40, 'status' => 'active'],
                ['school_id' => 1, 'subject_name' => 'Chemistry', 'subject_code' => 'CHEM', 'class_level' => '11', 'total_marks' => 100, 'passing_marks' => 40, 'status' => 'active'],
                ['school_id' => 1, 'subject_name' => 'Biology', 'subject_code' => 'BIO', 'class_level' => '11', 'total_marks' => 100, 'passing_marks' => 40, 'status' => 'active']
            ];

            foreach ($subjects as $subject) {
                Subject::firstOrCreate(['subject_name' => $subject['subject_name']], $subject);
            }

            // Get first teacher for sample exams
            $teacher = teachers::first();
            $teacherId = $teacher ? $teacher->id : null;

            // Create Sample Exams
            $mathSubject = Subject::where('subject_name', 'Mathematics')->first();
            $englishSubject = Subject::where('subject_name', 'English')->first();
            $scienceSubject = Subject::where('subject_name', 'Science')->first();
            $monthlyTestType = ExamType::where('exam_type_name', 'Monthly Test')->first();
            $quizType = ExamType::where('exam_type_name', 'Quiz')->first();

            $sampleExams = [
                [
                    'school_id' => 1,
                    'session' => '2024-25',
                    'exam_name' => 'Mathematics Monthly Test - Class 9',
                    'exam_type_id' => $monthlyTestType->id,
                    'class_id' => '9',
                    'class_name' => 'Class 9',
                    'subject_id' => $mathSubject->id,
                    'teacher_id' => $teacherId,
                    'exam_date' => now()->addDays(7)->toDateString(),
                    'exam_time' => now()->setTime(10, 0),
                    'duration_minutes' => 90,
                    'total_marks' => 50,
                    'passing_marks' => 20,
                    'total_questions' => 10,
                    'mcq_questions' => 5,
                    'short_questions' => 3,
                    'long_questions' => 2,
                    'instructions' => 'Read all questions carefully. Attempt all questions. Use only blue/black pen.',
                    'status' => 'published',
                    'auto_submit' => true,
                    'show_results' => true,
                    'randomize_questions' => false,
                    'created_by' => 1
                ],
                [
                    'school_id' => 1,
                    'session' => '2024-25',
                    'exam_name' => 'English Grammar Quiz - Class 8',
                    'exam_type_id' => $quizType->id,
                    'class_id' => '8',
                    'class_name' => 'Class 8',
                    'subject_id' => $englishSubject->id,
                    'teacher_id' => $teacherId,
                    'exam_date' => now()->addDays(3)->toDateString(),
                    'exam_time' => now()->setTime(11, 30),
                    'duration_minutes' => 30,
                    'total_marks' => 20,
                    'passing_marks' => 8,
                    'total_questions' => 10,
                    'mcq_questions' => 10,
                    'short_questions' => 0,
                    'long_questions' => 0,
                    'instructions' => 'This is a quick grammar quiz. Choose the best answer.',
                    'status' => 'published',
                    'auto_submit' => true,
                    'show_results' => true,
                    'randomize_questions' => true,
                    'created_by' => 1
                ],
                [
                    'school_id' => 1,
                    'session' => '2024-25',
                    'exam_name' => 'Science Chapter Test - Class 10',
                    'exam_type_id' => $monthlyTestType->id,
                    'class_id' => '10',
                    'class_name' => 'Class 10',
                    'subject_id' => $scienceSubject->id,
                    'teacher_id' => $teacherId,
                    'exam_date' => now()->addDays(10)->toDateString(),
                    'exam_time' => now()->setTime(14, 0),
                    'duration_minutes' => 60,
                    'total_marks' => 40,
                    'passing_marks' => 16,
                    'total_questions' => 8,
                    'mcq_questions' => 4,
                    'short_questions' => 2,
                    'long_questions' => 2,
                    'instructions' => 'Answer all questions. Draw neat diagrams where required.',
                    'status' => 'draft',
                    'auto_submit' => true,
                    'show_results' => false,
                    'randomize_questions' => false,
                    'created_by' => 1
                ]
            ];

            foreach ($sampleExams as $examData) {
                $exam = Exam::create($examData);
                
                // Create questions for each exam
                $this->createQuestionsForExam($exam);
            }

            // Create Question Bank entries
            $this->createQuestionBank();

            // Create sample exam attempts and results
            $this->createSampleResults();

            DB::commit();
            $this->command->info('Exam system data seeded successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Error seeding exam data: ' . $e->getMessage());
        }
    }

    private function createQuestionsForExam($exam)
    {
        $questionNumber = 1;

        // Create MCQ questions
        if ($exam->mcq_questions > 0) {
            $mcqQuestions = $this->getMCQQuestions($exam->subject->subject_name, $exam->mcq_questions);
            
            foreach ($mcqQuestions as $questionData) {
                $question = ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_number' => $questionNumber++,
                    'question_type' => 'mcq',
                    'question_text' => $questionData['question'],
                    'marks' => $questionData['marks'],
                    'difficulty_level' => $questionData['difficulty'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'status' => 'active'
                ]);

                // Create MCQ options
                foreach ($questionData['options'] as $index => $option) {
                    McqOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option,
                        'is_correct' => ($index === $questionData['correct'])
                    ]);
                }
            }
        }

        // Create short questions
        if ($exam->short_questions > 0) {
            $shortQuestions = $this->getShortQuestions($exam->subject->subject_name, $exam->short_questions);
            
            foreach ($shortQuestions as $questionData) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_number' => $questionNumber++,
                    'question_type' => 'short',
                    'question_text' => $questionData['question'],
                    'marks' => $questionData['marks'],
                    'difficulty_level' => $questionData['difficulty'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'status' => 'active'
                ]);
            }
        }

        // Create long questions
        if ($exam->long_questions > 0) {
            $longQuestions = $this->getLongQuestions($exam->subject->subject_name, $exam->long_questions);
            
            foreach ($longQuestions as $questionData) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_number' => $questionNumber++,
                    'question_type' => 'long',
                    'question_text' => $questionData['question'],
                    'marks' => $questionData['marks'],
                    'difficulty_level' => $questionData['difficulty'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'status' => 'active'
                ]);
            }
        }
    }

    private function getMCQQuestions($subject, $count)
    {
        $questions = [
            'Mathematics' => [
                [
                    'question' => 'What is the value of π (pi) approximately?',
                    'options' => ['3.14159', '2.71828', '1.41421', '1.73205'],
                    'correct' => 0,
                    'marks' => 2,
                    'difficulty' => 'easy',
                    'explanation' => 'π (pi) is approximately 3.14159'
                ],
                [
                    'question' => 'What is the square root of 144?',
                    'options' => ['12', '14', '10', '16'],
                    'correct' => 0,
                    'marks' => 2,
                    'difficulty' => 'easy'
                ],
                [
                    'question' => 'If x + 5 = 12, what is the value of x?',
                    'options' => ['7', '17', '5', '12'],
                    'correct' => 0,
                    'marks' => 3,
                    'difficulty' => 'medium'
                ],
                [
                    'question' => 'What is the area of a circle with radius 5 units?',
                    'options' => ['25π', '10π', '50π', '5π'],
                    'correct' => 0,
                    'marks' => 4,
                    'difficulty' => 'medium'
                ],
                [
                    'question' => 'What is the derivative of x² + 3x + 2?',
                    'options' => ['2x + 3', 'x² + 3', '2x + 2', 'x + 3'],
                    'correct' => 0,
                    'marks' => 5,
                    'difficulty' => 'hard'
                ]
            ],
            'English' => [
                [
                    'question' => 'Which of the following is a noun?',
                    'options' => ['quickly', 'book', 'run', 'beautiful'],
                    'correct' => 1,
                    'marks' => 2,
                    'difficulty' => 'easy'
                ],
                [
                    'question' => 'What is the past tense of "go"?',
                    'options' => ['goed', 'went', 'gone', 'going'],
                    'correct' => 1,
                    'marks' => 2,
                    'difficulty' => 'easy'
                ],
                [
                    'question' => 'Which sentence is grammatically correct?',
                    'options' => ['He don\'t like apples', 'He doesn\'t likes apples', 'He doesn\'t like apples', 'He not like apples'],
                    'correct' => 2,
                    'marks' => 3,
                    'difficulty' => 'medium'
                ],
                [
                    'question' => 'What is a synonym for "happy"?',
                    'options' => ['sad', 'angry', 'joyful', 'tired'],
                    'correct' => 2,
                    'marks' => 2,
                    'difficulty' => 'easy'
                ],
                [
                    'question' => 'Which literary device uses "like" or "as" for comparison?',
                    'options' => ['metaphor', 'simile', 'personification', 'alliteration'],
                    'correct' => 1,
                    'marks' => 3,
                    'difficulty' => 'medium'
                ]
            ],
            'Science' => [
                [
                    'question' => 'What is the chemical symbol for water?',
                    'options' => ['H2O', 'CO2', 'O2', 'H2SO4'],
                    'correct' => 0,
                    'marks' => 2,
                    'difficulty' => 'easy'
                ],
                [
                    'question' => 'Which planet is closest to the Sun?',
                    'options' => ['Venus', 'Earth', 'Mercury', 'Mars'],
                    'correct' => 2,
                    'marks' => 2,
                    'difficulty' => 'easy'
                ],
                [
                    'question' => 'What is the unit of electric current?',
                    'options' => ['Volt', 'Ampere', 'Ohm', 'Watt'],
                    'correct' => 1,
                    'marks' => 3,
                    'difficulty' => 'medium'
                ],
                [
                    'question' => 'Which gas is most abundant in Earth\'s atmosphere?',
                    'options' => ['Oxygen', 'Carbon Dioxide', 'Nitrogen', 'Hydrogen'],
                    'correct' => 2,
                    'marks' => 3,
                    'difficulty' => 'medium'
                ],
                [
                    'question' => 'What is the speed of light in vacuum?',
                    'options' => ['3 × 10⁸ m/s', '3 × 10⁶ m/s', '3 × 10¹⁰ m/s', '3 × 10⁴ m/s'],
                    'correct' => 0,
                    'marks' => 4,
                    'difficulty' => 'hard'
                ]
            ]
        ];

        $subjectQuestions = $questions[$subject] ?? $questions['Mathematics'];
        return array_slice($subjectQuestions, 0, $count);
    }

    private function getShortQuestions($subject, $count)
    {
        $questions = [
            'Mathematics' => [
                [
                    'question' => 'Solve for x: 2x + 7 = 15',
                    'marks' => 3,
                    'difficulty' => 'medium',
                    'explanation' => '2x = 15 - 7 = 8, so x = 4'
                ],
                [
                    'question' => 'Find the perimeter of a rectangle with length 8cm and width 5cm.',
                    'marks' => 3,
                    'difficulty' => 'easy',
                    'explanation' => 'Perimeter = 2(l + w) = 2(8 + 5) = 26cm'
                ],
                [
                    'question' => 'What is the sum of interior angles of a triangle?',
                    'marks' => 2,
                    'difficulty' => 'easy',
                    'explanation' => 'The sum is always 180 degrees'
                ]
            ],
            'English' => [
                [
                    'question' => 'Write the plural form of "child".',
                    'marks' => 2,
                    'difficulty' => 'easy',
                    'explanation' => 'Children'
                ],
                [
                    'question' => 'Define a metaphor and give one example.',
                    'marks' => 4,
                    'difficulty' => 'medium',
                    'explanation' => 'A metaphor is a direct comparison without using like or as. Example: Time is money.'
                ],
                [
                    'question' => 'Convert this sentence to passive voice: "The cat caught the mouse."',
                    'marks' => 3,
                    'difficulty' => 'medium',
                    'explanation' => 'The mouse was caught by the cat.'
                ]
            ],
            'Science' => [
                [
                    'question' => 'Name the three states of matter.',
                    'marks' => 3,
                    'difficulty' => 'easy',
                    'explanation' => 'Solid, liquid, and gas'
                ],
                [
                    'question' => 'What is photosynthesis? Write the equation.',
                    'marks' => 4,
                    'difficulty' => 'medium',
                    'explanation' => 'Process by which plants make food using sunlight. 6CO2 + 6H2O → C6H12O6 + 6O2'
                ],
                [
                    'question' => 'Define Newton\'s first law of motion.',
                    'marks' => 3,
                    'difficulty' => 'medium',
                    'explanation' => 'An object at rest stays at rest and an object in motion stays in motion unless acted upon by an external force.'
                ]
            ]
        ];

        $subjectQuestions = $questions[$subject] ?? $questions['Mathematics'];
        return array_slice($subjectQuestions, 0, $count);
    }

    private function getLongQuestions($subject, $count)
    {
        $questions = [
            'Mathematics' => [
                [
                    'question' => 'A rectangular garden has a length of 15 meters and a width of 10 meters. If a path of uniform width x meters is built around the garden, and the total area including the path is 300 square meters, find the width of the path.',
                    'marks' => 8,
                    'difficulty' => 'hard',
                    'explanation' => 'Set up equation: (15+2x)(10+2x) = 300. Expand and solve quadratic equation.'
                ],
                [
                    'question' => 'Prove that the sum of first n natural numbers is n(n+1)/2. Also find the sum of first 50 natural numbers.',
                    'marks' => 10,
                    'difficulty' => 'hard',
                    'explanation' => 'Use mathematical induction or arithmetic series formula. Sum of first 50 = 50×51/2 = 1275'
                ]
            ],
            'English' => [
                [
                    'question' => 'Write a letter to your friend describing your experience of visiting a science museum. Include details about the exhibits you found most interesting and what you learned.',
                    'marks' => 8,
                    'difficulty' => 'medium',
                    'explanation' => 'Should include proper letter format, descriptive language, and personal reflection.'
                ],
                [
                    'question' => 'Compare and contrast the themes of friendship and loyalty in any two stories you have read. Provide examples from the texts to support your answer.',
                    'marks' => 10,
                    'difficulty' => 'hard',
                    'explanation' => 'Should analyze literary themes with specific textual evidence and clear comparison.'
                ]
            ],
            'Science' => [
                [
                    'question' => 'Explain the process of digestion in humans. Include the role of different organs and enzymes involved.',
                    'marks' => 8,
                    'difficulty' => 'medium',
                    'explanation' => 'Cover mechanical and chemical digestion, organs from mouth to intestine, and key enzymes.'
                ],
                [
                    'question' => 'Describe the carbon cycle in detail. Explain how human activities are affecting this natural cycle and suggest measures to restore balance.',
                    'marks' => 10,
                    'difficulty' => 'hard',
                    'explanation' => 'Cover all carbon reservoirs, processes, human impact like deforestation and fossil fuels, and conservation measures.'
                ]
            ]
        ];

        $subjectQuestions = $questions[$subject] ?? $questions['Mathematics'];
        return array_slice($subjectQuestions, 0, $count);
    }

    private function createQuestionBank()
    {
        $subjects = Subject::all();
        
        foreach ($subjects as $subject) {
            // Create some general questions for each subject
            $questions = [
                [
                    'school_id' => 1,
                    'subject_id' => $subject->id,
                    'class_level' => '9',
                    'question_text' => 'Sample ' . $subject->subject_name . ' question for Class 9 - What is the main concept in this chapter?',
                    'question_type' => 'mcq',
                    'difficulty_level' => 'medium',
                    'marks' => 3,
                    'explanation' => 'This is a sample explanation for the question.',
                    'tags' => ['basic', 'concept', 'chapter1'],
                    'created_by' => 1,
                    'status' => 'active'
                ],
                [
                    'school_id' => 1,
                    'subject_id' => $subject->id,
                    'class_level' => '10',
                    'question_text' => 'Explain the importance of ' . $subject->subject_name . ' in daily life.',
                    'question_type' => 'short',
                    'difficulty_level' => 'easy',
                    'marks' => 4,
                    'explanation' => 'Students should explain practical applications.',
                    'tags' => ['application', 'daily-life'],
                    'created_by' => 1,
                    'status' => 'active'
                ]
            ];

            foreach ($questions as $questionData) {
                // Normalize tags: DB column is string, so store comma-separated list if provided as array
                if (isset($questionData['tags']) && is_array($questionData['tags'])) {
                    $questionData['tags'] = implode(',', $questionData['tags']);
                }

                $question = QuestionBank::create($questionData);
                
                // Add MCQ options for MCQ questions
                if ($questionData['question_type'] === 'mcq') {
                    $options = [
                        ['question_id' => $question->id, 'option_text' => 'Option A - Correct answer', 'is_correct' => true],
                        ['question_id' => $question->id, 'option_text' => 'Option B - Wrong answer', 'is_correct' => false],
                        ['question_id' => $question->id, 'option_text' => 'Option C - Wrong answer', 'is_correct' => false],
                        ['question_id' => $question->id, 'option_text' => 'Option D - Wrong answer', 'is_correct' => false]
                    ];

                    foreach ($options as $option) {
                        QuestionBankOption::create($option);
                    }
                }
            }
        }
    }

    private function createSampleResults()
    {
        $students = students::take(5)->get();
        $publishedExams = Exam::where('status', 'published')->get();

        foreach ($publishedExams as $exam) {
            foreach ($students as $student) {
                // Create exam attempt
                $attempt = StudentExamAttempt::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'started_at' => now()->subHours(2),
                    'submitted_at' => now()->subHours(1),
                    'status' => 'submitted',
                    'ip_address' => '192.168.1.1',
                    'user_agent' => 'Sample Browser'
                ]);

                // Create exam result
                $percentage = rand(40, 95);
                $obtainedMarks = ($percentage / 100) * $exam->total_marks;
                $status = $obtainedMarks >= $exam->passing_marks ? 'pass' : 'fail';

                ExamResult::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'attempt_id' => $attempt->id,
                    'obtained_marks' => $obtainedMarks,
                    'total_marks' => $exam->total_marks,
                    'percentage' => $percentage,
                    'grade' => $this->calculateGrade($percentage),
                    'status' => $status,
                    'remarks' => $status === 'pass' ? 'Good performance' : 'Needs improvement'
                ]);

                // Create sample answers for some questions
                $questions = $exam->questions()->take(3)->get();
                foreach ($questions as $question) {
                    $isCorrect = rand(0, 1);
                    $obtainedMarks = $isCorrect ? $question->marks : rand(0, $question->marks - 1);

                    StudentAnswer::create([
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'answer_text' => $question->isMcq() ? 'Option A' : 'Sample student answer',
                        'selected_option_id' => $question->isMcq() ? $question->mcqOptions->first()->id : null,
                        'is_correct' => $isCorrect,
                        'obtained_marks' => $obtainedMarks,
                        'total_marks' => $question->marks
                    ]);
                }
            }
        }
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        return 'F';
    }
}
