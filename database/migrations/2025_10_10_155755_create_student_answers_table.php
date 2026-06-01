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
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->enum('question_type', ['mcq', 'short', 'long', 'true_false', 'fill_blank']);
            $table->string('selected_option')->nullable(); // For MCQ answers (A, B, C, D)
            $table->text('answer_text')->nullable(); // For text-based answers
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->text('teacher_remarks')->nullable();
            $table->datetime('answered_at')->nullable();
            $table->datetime('graded_at')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable();
            $table->timestamps();
            
            $table->foreign('attempt_id')->references('id')->on('student_exam_attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('exam_questions')->onDelete('cascade');
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['attempt_id', 'question_id']);
            $table->index('question_type');
            $table->index('is_correct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};

