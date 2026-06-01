<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('students')) {
            $this->command?->warn('students table not found; skipping StudentsSeeder');
            return;
        }

        $classes = DB::table('classes')->orderBy('id')->get();
        if ($classes->isEmpty()) {
            $this->command?->warn('No classes found; run PakistaniClassesSeeder first.');
            return;
        }

        $parents = DB::table('parents')->pluck('id')->toArray();
        if (empty($parents)) {
            // Ensure at least 10 parents exist
            (new ParentsSeeder())->run();
            $parents = DB::table('parents')->pluck('id')->toArray();
        }

        $grStart = (int) (DB::table('students')->max(DB::raw('CAST(grno AS UNSIGNED)')) ?? 1000);
        $created = 0;

        // Pakistani names pools
        $maleFirst = ['Ahmed','Muhammad','Ali','Hassan','Hussain','Usman','Bilal','Hamza','Salman','Faisal','Imran','Shahzad','Naveed','Zeeshan','Waqar','Rehan','Arslan','Ahsan','Saad','Farhan'];
        $femaleFirst = ['Ayesha','Fatima','Zainab','Maryam','Hira','Sana','Sara','Maira','Khadija','Laiba','Iqra','Mehwish','Aiman','Hina','Mishal','Mariam','Anum','Sadia','Nimra','Kiran'];
        $lastNames = ['Khan','Ahmad','Malik','Sheikh','Chaudhry','Hussain','Butt','Raza','Qureshi','Anwar','Farooq','Yousaf','Ashraf','Iftikhar','Javed','Nadeem','Akhtar','Arif','Aziz','Shahid'];

        foreach ($classes as $class) {
            // Count existing students for this class
            $existing = DB::table('students')->where('class_id', $class->id)->count();
            $toMake = max(0, 10 - (int)$existing);
            $sections = ['A','B'];
            for ($i = 1; $i <= $toMake; $i++) {
                $grStart++;
                $section = $sections[($i - 1) % count($sections)];
                $parentId = $parents[array_rand($parents)];
                $gender = (rand(0,1) ? 'Male' : 'Female');
                $first = $gender === 'Male' ? $maleFirst[array_rand($maleFirst)] : $femaleFirst[array_rand($femaleFirst)];
                $last = $lastNames[array_rand($lastNames)];
                $fullName = $first.' '.$last;
                DB::table('students')->insert([
                    'studentName' => $fullName,
                    'class_id' => $class->id,
                    'section' => $section,
                    'status' => 'active',
                    'parent_id' => $parentId,
                    'grno' => (string)$grStart,
                    'school_id' => 1,
                    'session' => '2024-2025',
                    'gender' => $gender,
                    'date_of_birth' => now()->subYears(rand(5,18))->subDays(rand(0,365))->toDateString(),
                    'address' => 'Street ' . rand(1,50) . ', Karachi',
                    'phone' => '03' . str_pad((string)rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                    'email' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }
        }
        $this->command?->info("Students seeded: {$created}");
    }
}