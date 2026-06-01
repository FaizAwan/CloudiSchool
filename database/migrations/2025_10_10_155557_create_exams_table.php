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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->string('session')->nullable();
            $table->string('exam_name');
            $table->unsignedBigInteger('exam_type_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('class_name')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->date('exam_date')->nullable();
            $table->datetime('exam_time')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->integer('total_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->integer('total_questions')->default(0);
            $table->integer('mcq_questions')->default(0);
            $table->integer('short_questions')->default(0);
            $table->integer('long_questions')->default(0);
            $table->text('instructions')->nullable();
            $table->enum('status', ['draft', 'published', 'completed', 'cancelled'])->default('draft');
            $table->boolean('auto_submit')->default(true);
            $table->boolean('show_results')->default(true);
            $table->boolean('randomize_questions')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->index(['school_id', 'status']);
            $table->index(['exam_date', 'status']);
            $table->index(['class_id', 'subject_id']);
            $table->index('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
