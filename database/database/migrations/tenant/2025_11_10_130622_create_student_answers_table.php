<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id')->index('student_answers_question_id_foreign');
            $table->enum('question_type', ['mcq', 'short', 'long', 'true_false', 'fill_blank'])->index();
            $table->string('selected_option')->nullable();
            $table->text('answer_text')->nullable();
            $table->tinyInteger('is_correct')->nullable()->index();
            $table->decimal('marks_obtained')->nullable();
            $table->text('teacher_remarks')->nullable();
            $table->dateTime('answered_at')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable()->index('student_answers_graded_by_foreign');
            $table->timestamps();

            $table->index(['attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_answers');
    }
}
