<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\students;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateStudentCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:generate-credentials {--show : Show the generated credentials}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate login credentials for all students based on their roll numbers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🎓 Generating student login credentials...');
        
        $students = students::with('user')->get();
        $created = 0;
        $existing = 0;
        $credentials = [];
        
        $this->withProgressBar($students, function ($student) use (&$created, &$existing, &$credentials) {
            if (!$student->user) {
                // Generate email and password based on roll number
                $email = 'student' . $student->grno . '@school.edu';
                $password = 'pass_' . $student->grno;
                
                // Create user account for student
                $user = User::create([
                    'name' => $student->studentName,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'student',
                    'school_id' => $student->school_id ?? 1,
                ]);
                
                // Update student record with user_id
                $student->update(['user_id' => $user->id]);
                
                $credentials[] = [
                    'name' => $student->studentName,
                    'roll_number' => $student->grno,
                    'email' => $email,
                    'password' => $password,
                    'class' => $student->class ? $student->class->className : 'N/A',
                    'status' => 'NEW'
                ];
                
                $created++;
            } else {
                $credentials[] = [
                    'name' => $student->studentName,
                    'roll_number' => $student->grno,
                    'email' => $student->user->email,
                    'password' => 'pass_' . $student->grno . ' (default)',
                    'class' => $student->class ? $student->class->className : 'N/A',
                    'status' => 'EXISTS'
                ];
                
                $existing++;
            }
        });
        
        $this->newLine(2);
        $this->info('✅ Student credential generation completed!');
        $this->table(['Metric', 'Count'], [
            ['New accounts created', $created],
            ['Existing accounts', $existing],
            ['Total students', count($students)]
        ]);
        
        if ($this->option('show')) {
            $this->newLine();
            $this->info('📋 Generated Credentials:');
            $this->table(
                ['Name', 'Roll No.', 'Email', 'Password', 'Class', 'Status'],
                array_map(function($cred) {
                    return [
                        Str::limit($cred['name'], 20),
                        $cred['roll_number'],
                        $cred['email'],
                        $cred['password'],
                        $cred['class'],
                        $cred['status']
                    ];
                }, $credentials)
            );
        } else {
            $this->info('💡 Use --show flag to display all credentials');
        }
        
        $this->newLine();
        $this->info('🔐 Student Login Instructions:');
        $this->line('• Email format: student[ROLL_NUMBER]@school.edu');
        $this->line('• Password format: pass_[ROLL_NUMBER]');
        $this->line('• Example: Email: student123@school.edu, Password: pass_123');
        
        return 0;
    }
}
