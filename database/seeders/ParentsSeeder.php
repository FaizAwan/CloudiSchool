<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ParentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('parents')) {
            $this->command?->warn('parents table not found; skipping ParentsSeeder');
            return;
        }

        // 1) Remove placeholder rows like "Parent 1", "Father 1", etc.
        $deleted = DB::table('parents')
            ->where('parentName', 'like', 'Parent %')
            ->orWhere('fatherName', 'like', 'Father %')
            ->orWhere('motherName', 'like', 'Mother %')
            ->orWhere('email', 'like', 'parent%@example.com')
            ->delete();
        if ($deleted > 0) {
            $this->command?->info("Removed {$deleted} placeholder parent records.");
        }

        // 2) Seed with Pakistani parent names (idempotent via email)
        $pakistaniParents = [
            [
                'parentName' => 'Muhammad Ali',
                'fatherName' => 'Muhammad Ali',
                'motherName' => 'Ayesha Ali',
                'email' => 'm.ali@example.pk',
                'phone' => '03001234567',
                'address' => 'Gulshan-e-Iqbal, Karachi',
                'occupation' => 'Businessman',
            ],
            [
                'parentName' => 'Ayesha Khan',
                'fatherName' => 'Ahmed Khan',
                'motherName' => 'Ayesha Khan',
                'email' => 'ayesha.khan@example.pk',
                'phone' => '03019876543',
                'address' => 'Model Town, Lahore',
                'occupation' => 'Teacher',
            ],
            [
                'parentName' => 'Ahmed Raza',
                'fatherName' => 'Ahmed Raza',
                'motherName' => 'Sadia Raza',
                'email' => 'ahmed.raza@example.pk',
                'phone' => '03111222333',
                'address' => 'F-8, Islamabad',
                'occupation' => 'Engineer',
            ],
            [
                'parentName' => 'Sara Yousaf',
                'fatherName' => 'Yousaf Ali',
                'motherName' => 'Sara Yousaf',
                'email' => 'sara.yousaf@example.pk',
                'phone' => '03215556666',
                'address' => 'Cantt, Rawalpindi',
                'occupation' => 'Doctor',
            ],
            [
                'parentName' => 'Bilal Hussain',
                'fatherName' => 'Bilal Hussain',
                'motherName' => 'Hina Bilal',
                'email' => 'bilal.hussain@example.pk',
                'phone' => '03337778888',
                'address' => 'University Road, Peshawar',
                'occupation' => 'Govt. Service',
            ],
            [
                'parentName' => 'Zainab Fatima',
                'fatherName' => 'Zubair Ahmad',
                'motherName' => 'Zainab Fatima',
                'email' => 'zainab.fatima@example.pk',
                'phone' => '03440001111',
                'address' => 'Satellite Town, Quetta',
                'occupation' => 'Homemaker',
            ],
            [
                'parentName' => 'Usman Ghani',
                'fatherName' => 'Usman Ghani',
                'motherName' => 'Abeer Usman',
                'email' => 'usman.ghani@example.pk',
                'phone' => '03004567890',
                'address' => 'People\'s Colony, Faisalabad',
                'occupation' => 'Shop Owner',
            ],
            [
                'parentName' => 'Maryam Nawaz',
                'fatherName' => 'Nawaz Ahmed',
                'motherName' => 'Maryam Nawaz',
                'email' => 'maryam.nawaz@example.pk',
                'phone' => '03005554444',
                'address' => 'Gulgasht Colony, Multan',
                'occupation' => 'Banker',
            ],
            [
                'parentName' => 'Hamza Iqbal',
                'fatherName' => 'Hamza Iqbal',
                'motherName' => 'Rida Hamza',
                'email' => 'hamza.iqbal@example.pk',
                'phone' => '03117779999',
                'address' => 'Latifabad, Hyderabad',
                'occupation' => 'Software Developer',
            ],
            [
                'parentName' => 'Fatima Zahra',
                'fatherName' => 'Zahid Hussain',
                'motherName' => 'Fatima Zahra',
                'email' => 'fatima.zahra@example.pk',
                'phone' => '03219998877',
                'address' => 'Civil Lines, Sialkot',
                'occupation' => 'Lecturer',
            ],
            [
                'parentName' => 'Imran Akhtar',
                'fatherName' => 'Imran Akhtar',
                'motherName' => 'Saima Imran',
                'email' => 'imran.akhtar@example.pk',
                'phone' => '03331112222',
                'address' => 'DHA, Karachi',
                'occupation' => 'Accountant',
            ],
            [
                'parentName' => 'Hira Siddiqui',
                'fatherName' => 'Saeed Siddiqui',
                'motherName' => 'Hira Siddiqui',
                'email' => 'hira.siddiqui@example.pk',
                'phone' => '03442223333',
                'address' => 'Johar Town, Lahore',
                'occupation' => 'Entrepreneur',
            ],
            [
                'parentName' => 'Kashif Mehmood',
                'fatherName' => 'Kashif Mehmood',
                'motherName' => 'Aqsa Kashif',
                'email' => 'kashif.mehmood@example.pk',
                'phone' => '03006667777',
                'address' => 'G-11, Islamabad',
                'occupation' => 'Civil Engineer',
            ],
            [
                'parentName' => 'Sana Javed',
                'fatherName' => 'Javed Iqbal',
                'motherName' => 'Sana Javed',
                'email' => 'sana.javed@example.pk',
                'phone' => '03115554444',
                'address' => 'Bahria Town, Rawalpindi',
                'occupation' => 'HR Manager',
            ],
            [
                'parentName' => 'Noman Arshad',
                'fatherName' => 'Noman Arshad',
                'motherName' => 'Amna Noman',
                'email' => 'noman.arshad@example.pk',
                'phone' => '03214445555',
                'address' => 'Hayatabad, Peshawar',
                'occupation' => 'Pharmacist',
            ],
            [
                'parentName' => 'Rabia Anwar',
                'fatherName' => 'Anwar Hussain',
                'motherName' => 'Rabia Anwar',
                'email' => 'rabia.anwar@example.pk',
                'phone' => '03336663333',
                'address' => 'Samungli Road, Quetta',
                'occupation' => 'Doctor',
            ],
            [
                'parentName' => 'Junaid Tariq',
                'fatherName' => 'Junaid Tariq',
                'motherName' => 'Sehrish Junaid',
                'email' => 'junaid.tariq@example.pk',
                'phone' => '03441110000',
                'address' => 'Canal Road, Faisalabad',
                'occupation' => 'Lawyer',
            ],
            [
                'parentName' => 'Iqra Aslam',
                'fatherName' => 'Aslam Khan',
                'motherName' => 'Iqra Aslam',
                'email' => 'iqra.aslam@example.pk',
                'phone' => '03001239876',
                'address' => 'Cantt, Multan',
                'occupation' => 'Lecturer',
            ],
            [
                'parentName' => 'Adeel Sheikh',
                'fatherName' => 'Adeel Sheikh',
                'motherName' => 'Sidra Adeel',
                'email' => 'adeel.sheikh@example.pk',
                'phone' => '03119997777',
                'address' => 'North Nazimabad, Karachi',
                'occupation' => 'Marketing Manager',
            ],
            [
                'parentName' => 'Huma Qureshi',
                'fatherName' => 'Quresh Ali',
                'motherName' => 'Huma Qureshi',
                'email' => 'huma.qureshi@example.pk',
                'phone' => '03218886666',
                'address' => 'Gulberg, Lahore',
                'occupation' => 'Architect',
            ],
        ];

        $inserted = 0;
        foreach ($pakistaniParents as $p) {
            $exists = DB::table('parents')->where('email', $p['email'])->exists();
            if (!$exists) {
                DB::table('parents')->insert([
                    'parentName' => $p['parentName'],
                    'fatherName' => $p['fatherName'],
                    'motherName' => $p['motherName'],
                    'phone' => $p['phone'],
                    'email' => $p['email'],
                    'address' => $p['address'],
                    'occupation' => $p['occupation'],
                    'is_commandercityschool_employee' => 'No',
                    'status' => 'active',
                    'school_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            }
        }

        $this->command?->info("Pakistani parents seeded: {$inserted}");
    }
}
