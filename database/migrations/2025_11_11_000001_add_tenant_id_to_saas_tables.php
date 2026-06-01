<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tables = [
            'users',
            'students',
            'teachers',
            'sections',
            'classes',
            'fees',
            'challans',
            'attendances',
            'teacher_attendances',
            'exams',
            'exam_types',
            'exam_results',
            'exam_questions',
            'question_banks',
            'question_bank_options',
            'student_exam_attempts',
            'student_answers',
            'timetables',
            'parents',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            if (! Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
                });
            }
            // Add FK if schools exists
            if (Schema::hasTable('schools')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    // Guard against duplicate FK names
                    $fkName = $table.'_tenant_id_foreign';
                    try {
                        $t->foreign('tenant_id', $fkName)->references('id')->on('schools')->onDelete('cascade');
                    } catch (\Throwable $e) {
                        // Ignore if FK already exists or driver doesn't support named FKs
                        try {
                            $t->foreign('tenant_id')->references('id')->on('schools')->onDelete('cascade');
                        } catch (\Throwable $e2) {}
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'users',
            'students',
            'teachers',
            'sections',
            'classes',
            'fees',
            'challans',
            'attendances',
            'teacher_attendances',
            'exams',
            'exam_types',
            'exam_results',
            'exam_questions',
            'question_banks',
            'question_bank_options',
            'student_exam_attempts',
            'student_answers',
            'timetables',
            'parents',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            if (Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) {
                    try { $t->dropForeign(['tenant_id']); } catch (\Throwable $e) {}
                    $t->dropColumn('tenant_id');
                });
            }
        }
    }
};
