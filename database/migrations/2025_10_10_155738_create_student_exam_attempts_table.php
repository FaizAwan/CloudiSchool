<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->integer('attempt_number')->default(1);
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->integer('duration_taken')->nullable(); // in minutes
            $table->integer('total_questions')->default(0);
            $table->integer('attempted_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->decimal('total_marks_obtained', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('grade')->nullable();
            $table->enum('status', ['started', 'submitted', 'auto_submitted', 'graded'])->default('started');
            $table->string('ip_address')->nullable();
            $table->text('browser_info')->nullable();
            $table->timestamps();
            
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->index(['exam_id', 'student_id']);
            $table->index('status');
            $table->unique(['exam_id', 'student_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_exam_attempts');
    }
};

