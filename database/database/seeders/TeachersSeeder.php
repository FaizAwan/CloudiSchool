<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TeachersSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('teachers')) {
            $this->command?->warn('teachers table not found; skipping TeachersSeeder');
            return;
        }

        // Remove placeholder teachers like "Teacher 1"
        $deleted = DB::table('teachers')->where('teacherName', 'like', 'Teacher %')->delete();
        if ($deleted > 0) {
            $this->command?->info("Removed {$deleted} placeholder teacher records.");
        }

        $pakistaniTeachers = [
            'Ahmed Raza','Ayesha Khan','Bilal Hussain','Zainab Fatima','Usman Ghani','Maryam Nawaz','Hamza Iqbal','Fatima Zahra','Imran Akhtar','Hira Siddiqui','Kashif Mehmood','Sana Javed','Noman Arshad','Rabia Anwar','Junaid Tariq','Iqra Aslam','Adeel Sheikh','Huma Qureshi','Salman Farooq','Mahnoor Ali','Shahid Mehmood','Kinza Tariq','Waqar Ahmed','Sameer Khan','Sehrish Batool','Hassan Ali','Nida Yousaf','Asim Raza','Rimsha Khalid','Zohaib Khan'
        ];

        $classIds = DB::table('classes')->pluck('id')->toArray();
        $inserted = 0;
        foreach ($pakistaniTeachers as $idx => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@example.pk';
            $exists = DB::table('teachers')->where('email', $email)->exists();
            if ($exists) continue;

            $classId = $classIds ? $classIds[array_rand($classIds)] : null;
            $className = $classId ? (DB::table('classes')->where('id', $classId)->value('className')) : null;

            DB::table('teachers')->insert([
                'teacherName' => $name,
                'teacher_name' => $name,
                'email' => $email,
                'phone' => '03' . str_pad((string)rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'class_id' => $classId,
                'className' => $className,
                'school_id' => 1,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
        }

        $this->command?->info('Teachers seeded: ' . $inserted);
    }
}
