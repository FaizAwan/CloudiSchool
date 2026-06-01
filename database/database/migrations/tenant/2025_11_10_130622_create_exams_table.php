<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->string('session')->nullable();
            $table->string('exam_name');
            $table->unsignedBigInteger('exam_type_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('class_name')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable()->index();
            $table->date('exam_date')->nullable();
            $table->dateTime('exam_time')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->integer('total_marks')->default(100);
            $table->integer('passing_marks')->default(40);
            $table->integer('total_questions')->default(0);
            $table->integer('mcq_questions')->default(0);
            $table->integer('short_questions')->default(0);
            $table->integer('long_questions')->default(0);
            $table->text('instructions')->nullable();
            $table->enum('status', ['draft', 'published', 'completed', 'cancelled'])->default('draft');
            $table->tinyInteger('auto_submit')->default(1);
            $table->tinyInteger('show_results')->default(1);
            $table->tinyInteger('randomize_questions')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['exam_date', 'status']);
            $table->index(['school_id', 'status']);
            $table->index(['class_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
