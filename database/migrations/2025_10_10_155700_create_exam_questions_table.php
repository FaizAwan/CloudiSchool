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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('question_bank_id')->nullable();
            $table->integer('question_number');
            $table->enum('question_type', ['mcq', 'short', 'long', 'true_false', 'fill_blank'])->default('mcq');
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->integer('marks')->default(1);
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->text('explanation')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('question_bank_id')->references('id')->on('question_bank')->onDelete('set null');
            $table->index(['exam_id', 'question_number']);
            $table->index('question_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};

