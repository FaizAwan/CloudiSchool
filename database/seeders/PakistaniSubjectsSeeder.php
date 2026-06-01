<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PakistaniSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Ensure the subjects table exists
        if (!Schema::hasTable('subjects')) {
            $this->command->error('Subjects table does not exist. Please run migrations first.');
            return;
        }

        // Get all classes from the database
        $classes = DB::table('classes')->pluck('id', 'className')->toArray();
        
        if (empty($classes)) {
            $this->command->warn('No classes found in database. Please add classes first.');
            return;
        }

        $this->command->info('Adding Pakistani educational subjects for all classes...');

        // Define subjects by education level
        $subjectsByLevel = [
            // Pre-Primary Level (Pre-Nursery, Nursery, Prep)
            'pre_primary' => [
                ['name' => 'English', 'total_marks' => 50, 'passing_marks' => 25],
                ['name' => 'Urdu', 'total_marks' => 50, 'passing_marks' => 25],
                ['name' => 'Mathematics', 'total_marks' => 50, 'passing_marks' => 25],
                ['name' => 'General Knowledge', 'total_marks' => 50, 'passing_marks' => 25],
                ['name' => 'Drawing & Coloring', 'total_marks' => 25, 'passing_marks' => 12],
                ['name' => 'Rhymes & Stories', 'total_marks' => 25, 'passing_marks' => 12],
            ],

            // Primary Level (Classes 1-5)
            'primary' => [
                ['name' => 'English', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Urdu', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Mathematics', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Science', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Social Studies', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Islamiyat', 'total_marks' => 50, 'passing_marks' => 20],
                ['name' => 'Tarjma-tul-Quran', 'total_marks' => 50, 'passing_marks' => 20],
                ['name' => 'Computer Studies', 'total_marks' => 50, 'passing_marks' => 20],
                ['name' => 'Art & Craft', 'total_marks' => 50, 'passing_marks' => 20],
                ['name' => 'Physical Education', 'total_marks' => 25, 'passing_marks' => 10],
            ],

            // Middle Level (Classes 6-8)
            'middle' => [
                ['name' => 'English', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Urdu', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Mathematics', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Science', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Social Studies', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Islamiyat', 'total_marks' => 75, 'passing_marks' => 30],
                ['name' => 'Tarjma-tul-Quran', 'total_marks' => 75, 'passing_marks' => 30],
                ['name' => 'Computer Studies', 'total_marks' => 75, 'passing_marks' => 30],
                ['name' => 'General Science', 'total_marks' => 100, 'passing_marks' => 40],
                ['name' => 'Geography', 'total_marks' => 75, 'passing_marks' => 30],
                ['name' => 'History', 'total_marks' => 75, 'passing_marks' => 30],
                ['name' => 'Civics', 'total_marks' => 50, 'passing_marks' => 20],
                ['name' => 'Health & Physical Education', 'total_marks' => 50, 'passing_marks' => 20],
                ['name' => 'Art & Drawing', 'total_marks' => 50, 'passing_marks' => 20],
            ],

            // Secondary Level (Classes 9-10)
            'secondary' => [
                ['name' => 'English', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Urdu', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Mathematics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Physics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Chemistry', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Biology', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Islamiyat', 'total_marks' => 75, 'passing_marks' => 25],
                ['name' => 'Pakistan Studies', 'total_marks' => 75, 'passing_marks' => 25],
                ['name' => 'Computer Science', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Geography', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'History', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Economics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Civics', 'total_marks' => 75, 'passing_marks' => 25],
                ['name' => 'Health & Physical Education', 'total_marks' => 50, 'passing_marks' => 17],
                ['name' => 'Art & Design', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Home Economics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Agriculture', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Business Studies', 'total_marks' => 100, 'passing_marks' => 33],
            ],

            // Higher Secondary Level (Classes 11-12)
            'higher_secondary' => [
                // Core Subjects
                ['name' => 'English', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Urdu', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Islamiyat', 'total_marks' => 50, 'passing_marks' => 17],
                ['name' => 'Pakistan Studies', 'total_marks' => 50, 'passing_marks' => 17],

                // Science Group
                ['name' => 'Mathematics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Physics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Chemistry', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Biology', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Computer Science', 'total_marks' => 100, 'passing_marks' => 33],

                // Arts/Humanities Group
                ['name' => 'Psychology', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Sociology', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Philosophy', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Islamic History', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Political Science', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Geography', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'History', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Arabic', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Persian', 'total_marks' => 100, 'passing_marks' => 33],

                // Commerce Group
                ['name' => 'Accounting', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Business Mathematics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Economics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Commerce', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Banking', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Statistics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Business Studies', 'total_marks' => 100, 'passing_marks' => 33],

                // Additional Subjects
                ['name' => 'Fine Arts', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Home Economics', 'total_marks' => 100, 'passing_marks' => 33],
                ['name' => 'Health & Physical Education', 'total_marks' => 50, 'passing_marks' => 17],
            ],
        ];

        // Additional specialized subjects for all levels
        $additionalSubjects = [
            ['name' => 'Quranic Studies', 'total_marks' => 50, 'passing_marks' => 20],
            ['name' => 'Hadith', 'total_marks' => 50, 'passing_marks' => 20],
            ['name' => 'Islamic Ethics', 'total_marks' => 50, 'passing_marks' => 20],
            ['name' => 'Seerat-un-Nabi', 'total_marks' => 50, 'passing_marks' => 20],
            ['name' => 'Moral Education', 'total_marks' => 25, 'passing_marks' => 10],
            ['name' => 'Environmental Science', 'total_marks' => 50, 'passing_marks' => 20],
            ['name' => 'Life Skills', 'total_marks' => 25, 'passing_marks' => 10],
            ['name' => 'Information Technology', 'total_marks' => 75, 'passing_marks' => 30],
        ];

        $totalInserted = 0;

        foreach ($classes as $className => $classId) {
            $this->command->info("Processing class: {$className}");
            
            // Determine which subjects to add based on class level
            $subjects = [];
            $classLower = strtolower($className);
            
            if (in_array($classLower, ['pre-nursery', 'nursery', 'prep'])) {
                $subjects = $subjectsByLevel['pre_primary'];
            } elseif (in_array($classLower, ['1', '2', '3', '4', '5'])) {
                $subjects = array_merge($subjectsByLevel['primary'], $additionalSubjects);
            } elseif (in_array($classLower, ['6', '7', '8'])) {
                $subjects = array_merge($subjectsByLevel['middle'], $additionalSubjects);
            } elseif (in_array($classLower, ['9', '10'])) {
                $subjects = array_merge($subjectsByLevel['secondary'], $additionalSubjects);
            } elseif (in_array($classLower, ['11', '12'])) {
                $subjects = array_merge($subjectsByLevel['higher_secondary'], $additionalSubjects);
            } else {
                // Default to middle level subjects for unrecognized classes
                $subjects = array_merge($subjectsByLevel['middle'], $additionalSubjects);
            }

            // Insert subjects for this class
            foreach ($subjects as $index => $subject) {
                $existingSubject = DB::table('subjects')
                    ->where('class_id', $classId)
                    ->where('subject_name', $subject['name'])
                    ->first();

                if (!$existingSubject) {
                    DB::table('subjects')->insert([
                        'class_id' => $classId,
                        'subject_name' => $subject['name'],
                        'total_marks' => $subject['total_marks'],
                        'passing_marks' => $subject['passing_marks'],
                        'sort_order' => $index + 1,
                        'status' => 'active',
                        'term' => null, // General subject, applicable to all terms
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $totalInserted++;
                } else {
                    // Update existing subject if marks have changed
                    DB::table('subjects')
                        ->where('id', $existingSubject->id)
                        ->update([
                            'total_marks' => $subject['total_marks'],
                            'passing_marks' => $subject['passing_marks'],
                            'status' => 'active',
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        $this->command->info("Successfully processed {$totalInserted} subjects for Pakistani educational system.");
        $this->command->info("Subjects added include:");
        $this->command->info("- Core subjects: English, Urdu, Mathematics, Science");
        $this->command->info("- Islamic subjects: Islamiyat, Tarjma-tul-Quran, Quranic Studies, Hadith");
        $this->command->info("- Social subjects: Pakistan Studies, Geography, History, Civics");
        $this->command->info("- Science subjects: Physics, Chemistry, Biology, Computer Science");
        $this->command->info("- Arts subjects: Fine Arts, Art & Drawing, Art & Craft");
        $this->command->info("- Physical subjects: Health & Physical Education, Physical Education");
        $this->command->info("- Vocational subjects: Home Economics, Agriculture, Business Studies");
        $this->command->info("- Additional subjects: Environmental Science, Life Skills, IT");
    }
}