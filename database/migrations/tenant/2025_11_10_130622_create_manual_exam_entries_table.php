<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualExamEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_exam_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('class_id');
            $table->string('student_id', 50);
            $table->string('subject');
            $table->string('term')->default('Mid Term');
            $table->json('data')->nullable();
            $table->json('absent_subjects')->nullable()->comment('JSON array of subjects where student was absent');
            $table->json('subjects_to_improve')->nullable()->comment('JSON array of subjects requiring improvement');
            $table->timestamps();

            $table->unique(['class_id', 'student_id', 'subject', 'term']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_exam_entries');
    }
}
