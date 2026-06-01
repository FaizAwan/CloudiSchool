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
        Schema::create('academicYears', function (Blueprint $table) {
            $table->id();
            $table->string('academicYear');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('is_active', ['yes', 'no', 'closed'])->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academicYears');
    }
};
