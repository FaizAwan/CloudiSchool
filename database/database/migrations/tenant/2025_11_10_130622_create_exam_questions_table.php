<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('question_bank_id')->nullable()->index('exam_questions_question_bank_id_foreign');
            $table->integer('question_number');
            $table->enum('question_type', ['mcq', 'short', 'long', 'true_false', 'fill_blank'])->default('mcq')->index();
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->integer('marks')->default(1);
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->text('explanation')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['exam_id', 'question_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_questions');
    }
}
