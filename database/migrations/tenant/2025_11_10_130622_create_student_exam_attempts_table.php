<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentExamAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_exam_attempts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id')->index('student_exam_attempts_student_id_foreign');
            $table->integer('attempt_number')->default(1);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('duration_taken')->nullable();
            $table->integer('total_questions')->default(0);
            $table->integer('attempted_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->decimal('total_marks_obtained')->default(0);
            $table->decimal('percentage', 5)->default(0);
            $table->string('grade')->nullable();
            $table->enum('status', ['started', 'submitted', 'auto_submitted', 'graded'])->default('started')->index();
            $table->string('ip_address')->nullable();
            $table->text('browser_info')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id', 'attempt_number']);
            $table->index(['exam_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_exam_attempts');
    }
}
