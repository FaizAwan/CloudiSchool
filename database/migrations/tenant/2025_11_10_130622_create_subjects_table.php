<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject_name')->index();
            $table->string('subject_code')->nullable()->unique();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('term', 50)->nullable();
            $table->decimal('total_marks', 8, 3)->default(0);
            $table->decimal('passing_marks', 8, 3)->default(0);
            $table->json('term_marks')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['class_id', 'status']);
            $table->index(['class_id', 'sort_order'], 'subjects_class_sort_idx');
            $table->index(['class_id', 'term', 'status'], 'subjects_class_term_status_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
