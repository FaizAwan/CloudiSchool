<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToStudentExamAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_exam_attempts', function (Blueprint $table) {
            $table->foreign(['student_id'])->references(['id'])->on('students')->onDelete('CASCADE');
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
        Schema::table('student_exam_attempts', function (Blueprint $table) {
            $table->dropForeign('student_exam_attempts_student_id_foreign');
            $table->dropForeign('student_exam_attempts_exam_id_foreign');
        });
    }
}
