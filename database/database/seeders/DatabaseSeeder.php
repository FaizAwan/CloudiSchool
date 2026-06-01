<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed in a dependency-aware order
        $this->call([
            SchoolsSeeder::class,
            PakistaniClassesSeeder::class,
            PakistaniSubjectsSeeder::class,
            FeetypesSeeder::class,
            ParentsSeeder::class,
            TeachersSeeder::class,
            StudentsSeeder::class,
            ReplacePlaceholderStudentsSeeder::class,
            FeesSeeder::class,
            ChallansSeeder::class,
            AcademicYearsSeeder::class,
            ExamSystemSeeder::class,
            SuperAdminSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
