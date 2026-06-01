<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateManualExamEntriesWithStringStudentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop existing tables and recreate them with correct data types
        Schema::dropIfExists('manual_exam_entries');
        Schema::dropIfExists('student_behavior_attributes');
        
        // Recreate manual_exam_entries with string student_id
        Schema::create('manual_exam_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('class_id');
            $table->string('student_id', 50); // String to match grno format (e.g., 'STU001')
            $table->string('subject');
            $table->string('term')->default('Mid Term');
            $table->json('data')->nullable();
            $table->timestamps();
            
            $table->unique(['class_id', 'student_id', 'subject', 'term']);
        });
        
        // Recreate student_behavior_attributes with string student_id
        Schema::create('student_behavior_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('student_id', 50); // String to match grno format
            $table->integer('class_id');
            $table->string('section', 50)->nullable();
            $table->string('session', 128)->nullable();
            $table->string('term', 64);
            $table->json('attributes'); // behavior attributes map
            $table->decimal('overall_average', 5, 2)->nullable();
            $table->timestamps();
            $table->unique(['student_id','class_id','section','session','term'], 'uniq_student_behavior_scope');
            $table->index(['class_id','section','term']);
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
        Schema::dropIfExists('student_behavior_attributes');
    }
}
