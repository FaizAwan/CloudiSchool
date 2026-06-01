<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_results', 'exam_id')) {
                $table->unsignedBigInteger('exam_id')->after('id');
            }
            if (!Schema::hasColumn('exam_results', 'student_id')) {
                $table->unsignedBigInteger('student_id')->after('exam_id');
            }
            if (!Schema::hasColumn('exam_results', 'attempt_id')) {
                $table->unsignedBigInteger('attempt_id')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('exam_results', 'total_marks')) {
                $table->integer('total_marks')->default(100)->after('attempt_id');
            }
            if (!Schema::hasColumn('exam_results', 'obtained_marks')) {
                $table->decimal('obtained_marks', 8, 2)->default(0)->after('total_marks');
            }
            if (!Schema::hasColumn('exam_results', 'percentage')) {
                $table->decimal('percentage', 5, 2)->default(0)->after('obtained_marks');
            }
            if (!Schema::hasColumn('exam_results', 'grade')) {
                $table->string('grade')->nullable()->after('percentage');
            }
            if (!Schema::hasColumn('exam_results', 'position')) {
                $table->integer('position')->nullable()->after('grade');
            }
            if (!Schema::hasColumn('exam_results', 'remarks')) {
                $table->text('remarks')->nullable()->after('position');
            }
            if (!Schema::hasColumn('exam_results', 'status')) {
                $table->string('status')->default('pass')->after('remarks');
            }
            if (!Schema::hasColumn('exam_results', 'graded_by')) {
                $table->unsignedBigInteger('graded_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('exam_results', 'graded_at')) {
                $table->timestamp('graded_at')->nullable()->after('graded_by');
            }

            // Indexes and FKs
            if (!Schema::hasColumn('exam_results', 'exam_id')) return; // safety
            $table->index(['exam_id', 'student_id']);
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('attempt_id')->references('id')->on('student_exam_attempts')->onDelete('set null');
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            // Drop FKs first if columns exist
            if (Schema::hasColumn('exam_results', 'exam_id')) {
                $table->dropForeign(['exam_id']);
            }
            if (Schema::hasColumn('exam_results', 'student_id')) {
                $table->dropForeign(['student_id']);
            }
            if (Schema::hasColumn('exam_results', 'attempt_id')) {
                $table->dropForeign(['attempt_id']);
            }
            if (Schema::hasColumn('exam_results', 'graded_by')) {
                $table->dropForeign(['graded_by']);
            }

            // Drop columns (guarded)
            foreach (['graded_at','graded_by','status','remarks','position','grade','percentage','obtained_marks','total_marks','attempt_id','student_id','exam_id'] as $col) {
                if (Schema::hasColumn('exam_results', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
