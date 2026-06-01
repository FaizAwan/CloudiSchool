<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToStudentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->foreign(['graded_by'])->references(['id'])->on('users')->onDelete('SET NULL');
            $table->foreign(['attempt_id'])->references(['id'])->on('student_exam_attempts')->onDelete('CASCADE');
            $table->foreign(['question_id'])->references(['id'])->on('exam_questions')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropForeign('student_answers_graded_by_foreign');
            $table->dropForeign('student_answers_attempt_id_foreign');
            $table->dropForeign('student_answers_question_id_foreign');
        });
    }
}
