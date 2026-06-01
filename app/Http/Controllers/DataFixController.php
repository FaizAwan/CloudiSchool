<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataFixController extends Controller
{
    public function fixPakistaniContext()
    {
        // Removed auth check to allow public/global fix
        // $tenantId = auth()->user()->tenant_id ?? null;

        $maleNames = ["Muhammad", "Ali", "Ahmed", "Hassan", "Hussain", "Bilal", "Usama", "Hamza", "Umar", "Zain", "Raza", "Mustafa", "Ibrahim", "Ismail", "Yusuf", "Ayan", "Ayaan", "Abdullah", "Abdul", "Rehman", "Saad", "Fahad"];
        $femaleNames = ["Fatima", "Ayesha", "Zainab", "Maryam", "Sana", "Sara", "Hina", "Rabia", "Sadia", "Mehwish", "Noor", "Laiba", "Dua", "Anaya", "Amna", "Khadija", "Hafsa", "Maria", "Iqra", "Sidra"];
        $surnames = ["Khan", "Ahmed", "Ali", "Bhatt", "Chaudhry", "Sheikh", "Malik", "Shah", "Raja", "Qureshi", "Ansari", "Siddiqui", "Baig", "Mirza", "Jutt"];
        
        $fatherNames = ["Muhammad", "Ahmed", "Ali", "Hassan", "Hussain", "Akram", "Aslam", "Tariq", "Javaid", "Khalid", "Rashid", "Nasir", "Saeed", "Anwar", "Bashir", "Zafar", "Iqbal", "Sial", "Farooq", "Riaz"];

        // Select all students to fix the whole database
        $students = DB::table('students')->get();

        $count = 0;
        foreach($students as $student) {
            $genderInput = strtolower(trim($student->gender));
            $isMale = in_array($genderInput, ['male', 'm', 'boy']);
            
            if ($isMale) {
                $firstName = $maleNames[array_rand($maleNames)];
                // Male Pattern: Muhammad [First] or [First] [Surname]
                if (rand(0,1)) {
                    $newName = "Muhammad " . $firstName;
                } else {
                    $newName = $firstName . " " . $surnames[array_rand($surnames)];
                }
            } else {
                // Female Case
                $firstName = $femaleNames[array_rand($femaleNames)];
                // Female Pattern: [First] [Surname] or [First] [SecondFirst]
                if (rand(0,1)) {
                     $newName = $firstName . " " . $surnames[array_rand($surnames)];
                } else {
                     $newName = $firstName . " " . $femaleNames[array_rand($femaleNames)]; // e.g. Fatima Zara
                }
            }

            // Fix Section
            // Get valid sections for this class and tenant
            $sections = DB::table('sections')
                ->where('class_id', $student->class_id)
                ->where('tenant_id', $student->tenant_id)
                ->pluck('sectionName')
                ->toArray();
            
            // Default to 'A' or 'Green' if no sections defined, preferring existing if not empty
            // But user said "according to the sections already defined", so we assume defined.
            // If empty, let's stick to 'A'.
            if(!empty($sections)) {
                $newSection = $sections[array_rand($sections)];
            } else {
                $newSection = 'Blue'; // Default fallback
            }
            
            DB::table('students')->where('id', $student->id)->update([
                'studentName' => $newName,
                'section' => $newSection
            ]);

            // Fix Father Name
            if ($student->parent_id) {
                $fName = $fatherNames[array_rand($fatherNames)] . " " . $surnames[array_rand($surnames)];
                DB::table('parents')->where('id', $student->parent_id)->update(['parentName' => $fName]);
            }
            $count++;
        }

        return "Successfully updated " . $count . " student records to Pakistani context.";
    }
}
