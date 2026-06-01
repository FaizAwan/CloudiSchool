<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupPakistaniSubjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subjects:setup-pakistani {--force : Force update existing subjects}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Pakistani educational system classes and subjects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Pakistani Educational System...');
        $this->newLine();

        // Check if classes exist
        $classCount = DB::table('classes')->count();
        if ($classCount == 0) {
            $this->info('No classes found. Adding Pakistani educational system classes...');
            Artisan::call('db:seed', ['--class' => 'PakistaniClassesSeeder']);
            $this->info('Classes added successfully!');
        } else {
            $this->info("Found {$classCount} existing classes.");
            if ($this->option('force') || $this->confirm('Do you want to add missing classes?', true)) {
                Artisan::call('db:seed', ['--class' => 'PakistaniClassesSeeder']);
                $this->info('Missing classes added!');
            }
        }

        $this->newLine();

        // Setup subjects
        $subjectCount = DB::table('subjects')->count();
        if ($subjectCount == 0 || $this->option('force')) {
            $this->info('Setting up Pakistani educational subjects...');
            Artisan::call('db:seed', ['--class' => 'PakistaniSubjectsSeeder']);
            $this->info('Subjects setup completed!');
        } else {
            $this->info("Found {$subjectCount} existing subjects.");
            if ($this->confirm('Do you want to update/add subjects?', true)) {
                Artisan::call('db:seed', ['--class' => 'PakistaniSubjectsSeeder']);
                $this->info('Subjects updated!');
            }
        }

        $this->newLine();
        $this->info('✅ Pakistani Educational System setup completed!');
        $this->newLine();
        
        // Show summary
        $finalClassCount = DB::table('classes')->count();
        $finalSubjectCount = DB::table('subjects')->count();
        
        $this->table([
            ['Classes', $finalClassCount],
            ['Subjects', $finalSubjectCount],
        ], ['Component', 'Count']);

        $this->newLine();
        $this->info('🌟 Your school management software is now ready with:');
        $this->info('📚 Complete Pakistani curriculum subjects including:');
        $this->info('   • Tarjma-tul-Quran, Islamiyat, Quranic Studies');
        $this->info('   • Health & Physical Education');
        $this->info('   • Pakistan Studies, Geography, History');
        $this->info('   • Computer Science, Information Technology');
        $this->info('   • Arts, Fine Arts, Home Economics');
        $this->info('   • And many more subjects for all class levels!');
        
        return Command::SUCCESS;
    }
}