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
            $table->id();
            $table->integer('student_id'); // GRNO
            $table->integer('class_id');
            $table->string('subject', 100)->default('all');
            $table->string('term', 64);
            $table->string('session', 128)->nullable();
            $table->integer('teacher_id')->nullable();
            $table->json('data'); // JSON data containing marks and other info
            $table->timestamps();

            // Indexes for performance
            $table->index(['class_id', 'term', 'subject']);
            $table->index(['student_id', 'term']);
            $table->index('teacher_id');
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
