<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReplacePlaceholderStudentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('students')) { return; }

        // Name pools (Pakistani)
        $maleFirst = ['Ahmed','Muhammad','Ali','Hassan','Hussain','Usman','Bilal','Hamza','Salman','Faisal','Imran','Shahzad','Naveed','Zeeshan','Waqar','Rehan','Arslan','Ahsan','Saad','Farhan'];
        $femaleFirst = ['Ayesha','Fatima','Zainab','Maryam','Hira','Sana','Sara','Maira','Khadija','Laiba','Iqra','Mehwish','Aiman','Hina','Mishal','Mariam','Anum','Sadia','Nimra','Kiran'];
        $lastNames = ['Khan','Ahmad','Malik','Sheikh','Chaudhry','Hussain','Butt','Raza','Qureshi','Anwar','Farooq','Yousaf','Ashraf','Iftikhar','Javed','Nadeem','Akhtar','Arif','Aziz','Shahid'];

        // Find placeholder names like "Student 1", "Class X Student 2", "Test 3", etc.
        $placeholders = DB::table('students')
            ->select('id','studentName','gender')
            ->where(function($q){
                $q->whereRaw("LOWER(studentName) LIKE 'student %'")
                  ->orWhereRaw("LOWER(studentName) LIKE '% student %'")
                  ->orWhereRaw("LOWER(studentName) LIKE 'test %'")
                  ->orWhereRaw("LOWER(studentName) REGEXP 'student[0-9 ]*$'")
                  ->orWhereRaw("LOWER(studentName) REGEXP 'class .* student [0-9]+'");
            })
            ->get();

        $used = [];
        $updated = 0;
        foreach ($placeholders as $row) {
            $gender = $row->gender === 'Female' ? 'Female' : ($row->gender === 'Male' ? 'Male' : (rand(0,1)?'Male':'Female'));
            $first = $gender === 'Male' ? $maleFirst[array_rand($maleFirst)] : $femaleFirst[array_rand($femaleFirst)];
            $last = $lastNames[array_rand($lastNames)];
            $name = $first.' '.$last;
            $tries = 0;
            while (isset($used[$name]) && $tries < 5) { // avoid repeats within this run
                $last = $lastNames[array_rand($lastNames)];
                $name = $first.' '.$last;
                $tries++;
            }
            $used[$name] = true;
            DB::table('students')->where('id', $row->id)->update(['studentName' => $name, 'updated_at' => now()]);
            $updated++;
        }

        $this->command?->info("Placeholder students renamed: {$updated}");
    }
}
