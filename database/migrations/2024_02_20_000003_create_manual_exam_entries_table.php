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
        if (!Schema::hasTable('manual_exam_entries')) {
            Schema::create('manual_exam_entries', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->string('subject')->index(); // 'all' or specific subject name
                $table->string('term')->index();
                $table->json('data')->nullable(); // Stores marks and other attributes
                $table->timestamps();
            });
        }
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
