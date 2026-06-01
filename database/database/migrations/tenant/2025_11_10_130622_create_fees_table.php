<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable()->index();
            $table->string('class_name')->nullable();
            $table->string('fee_description')->nullable();
            $table->decimal('amount', 10)->default(0);
            $table->enum('status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->timestamps();
            $table->string('month')->nullable();
            $table->string('month_name')->nullable();
            $table->integer('year')->nullable();
            $table->unsignedBigInteger('fee_type_id')->nullable();
            $table->decimal('fee_value', 10)->nullable();
            $table->string('session')->default('March 2024 to March 2025');
            $table->unsignedBigInteger('school_id')->default('1')->index('fees_school_id_foreign');
            $table->string('fee_name')->nullable();

            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees');
    }
}
