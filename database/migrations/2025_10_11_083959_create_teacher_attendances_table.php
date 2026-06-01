<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'leave', 'late'])->default('present');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('school_id')->default(1);
            $table->unsignedBigInteger('marked_by');
            $table->timestamps();
            
            // Create unique index to prevent duplicate entries
            $table->unique(['teacher_id', 'date'], 'teacher_attendance_unique');
            
            // Add foreign key constraints
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('marked_by')->references('id')->on('users')->onDelete('cascade');
            
            // Add indexes for better performance
            $table->index('date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_attendances');
    }
};
