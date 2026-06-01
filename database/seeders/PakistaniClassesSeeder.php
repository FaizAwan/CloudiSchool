<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PakistaniClassesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Ensure the classes table exists
        if (!Schema::hasTable('classes')) {
            $this->command->error('Classes table does not exist. Please run migrations first.');
            return;
        }

        $this->command->info('Adding Pakistani educational system classes...');

        // Define classes according to Pakistani education system
        $classes = [
            // Pre-Primary Level
            ['className' => 'Pre-Nursery', 'status' => 'active'],
            ['className' => 'Nursery', 'status' => 'active'],
            ['className' => 'Prep', 'status' => 'active'],
            
            // Primary Level (Classes 1-5)
            ['className' => '1', 'status' => 'active'],
            ['className' => '2', 'status' => 'active'],
            ['className' => '3', 'status' => 'active'],
            ['className' => '4', 'status' => 'active'],
            ['className' => '5', 'status' => 'active'],
            
            // Middle Level (Classes 6-8)
            ['className' => '6', 'status' => 'active'],
            ['className' => '7', 'status' => 'active'],
            ['className' => '8', 'status' => 'active'],
            
            // Secondary Level (Classes 9-10)
            ['className' => '9', 'status' => 'active'],
            ['className' => '10', 'status' => 'active'],
            
            // Higher Secondary Level (Classes 11-12)
            ['className' => '11', 'status' => 'active'],
            ['className' => '12', 'status' => 'active'],
        ];

        $totalInserted = 0;

        foreach ($classes as $class) {
            $existingClass = DB::table('classes')
                ->where('className', $class['className'])
                ->first();

            if (!$existingClass) {
                // Add school_id if the column exists and user has a school
                $insertData = [
                    'className' => $class['className'],
                    'status' => $class['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Check if school_id column exists and add it if needed
                if (Schema::hasColumn('classes', 'school_id')) {
                    // Try to get the first school or default to 1
                    $schoolId = DB::table('schools')->value('id');
                    if ($schoolId) {
                        $insertData['school_id'] = $schoolId;
                    } else {
                        $insertData['school_id'] = 1; // Default school ID
                    }
                }

                DB::table('classes')->insert($insertData);
                $totalInserted++;
                $this->command->info("Added class: {$class['className']}");
            } else {
                $this->command->info("Class {$class['className']} already exists, skipping...");
            }
        }

        $this->command->info("Successfully processed {$totalInserted} classes for Pakistani educational system.");
    }
}