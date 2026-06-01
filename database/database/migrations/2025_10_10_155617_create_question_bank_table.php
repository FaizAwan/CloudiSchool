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
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('class_level')->nullable();
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'short', 'long', 'true_false', 'fill_blank'])->default('mcq');
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('marks')->default(1);
            $table->integer('default_marks')->default(1);
            $table->text('correct_answer')->nullable();
            $table->string('topic')->nullable();
            $table->string('chapter')->nullable();
            $table->text('explanation')->nullable();
            $table->string('tags')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('usage_count')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->index(['school_id', 'subject_id']);
            $table->index(['question_type', 'difficulty_level']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_bank');
    }
};
