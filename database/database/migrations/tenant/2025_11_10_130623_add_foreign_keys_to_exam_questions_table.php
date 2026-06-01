<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->foreign(['question_bank_id'])->references(['id'])->on('question_bank')->onDelete('SET NULL');
            $table->foreign(['exam_id'])->references(['id'])->on('exams')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropForeign('exam_questions_question_bank_id_foreign');
            $table->dropForeign('exam_questions_exam_id_foreign');
        });
    }
}
