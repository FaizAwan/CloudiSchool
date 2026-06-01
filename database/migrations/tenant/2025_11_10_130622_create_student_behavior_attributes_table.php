<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentBehaviorAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_behavior_attributes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_id', 50);
            $table->integer('class_id');
            $table->string('section', 50)->nullable();
            $table->string('session', 128)->nullable();
            $table->string('term', 64);
            $table->json('attributes');
            $table->decimal('overall_average', 5)->nullable();
            $table->timestamps();

            $table->index(['class_id', 'section', 'term']);
            $table->unique(['student_id', 'class_id', 'section', 'session', 'term'], 'uniq_student_behavior_scope');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_behavior_attributes');
    }
}
