<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->string('day'); // Monday, Tuesday, etc.
            $table->unsignedBigInteger('period_id');
            $table->string('class');
            $table->string('subject');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            // Note: periods table will be created automatically by HomeController if it doesn't exist
            
            // Unique constraint to prevent double-booking
            $table->unique(['teacher_id', 'day', 'period_id'], 'teacher_day_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetables');
    }
}
