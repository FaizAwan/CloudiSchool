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
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id')->nullable()->index();
                $table->unsignedBigInteger('school_id')->nullable()->index();
                $table->string('subject_name');
                $table->string('subject_code')->nullable();
                $table->unsignedBigInteger('class_id')->index();
                $table->string('term')->nullable();
                $table->string('term_marks')->nullable();
                $table->decimal('total_marks', 8, 2)->default(100.00);
                $table->decimal('passing_marks', 8, 2)->default(33.00);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->integer('sort_order')->default(0);
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
        Schema::dropIfExists('subjects');
    }
}
