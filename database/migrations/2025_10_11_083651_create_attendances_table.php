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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->date('date');
            $table->string('session')->default('2024-2025');
            $table->enum('status', ['present', 'absent', 'leave', 'late'])->default('present');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('school_id')->default(1);
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            // Create composite unique index to prevent duplicate entries
            $table->unique(['student_id', 'class_id', 'date', 'session'], 'attendance_unique');
            
            // Add foreign key constraints
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Add indexes for better performance
            $table->index('date');
            $table->index('status');
            $table->index('session');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
