<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChallansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('class_name')->nullable();
            $table->string('month')->nullable();
            $table->integer('year')->nullable();
            $table->string('challan_number')->nullable()->unique();
            $table->decimal('amount', 10)->default(0);
            $table->enum('status', ['generated', 'paid', 'cancelled'])->default('generated');
            $table->enum('paid', ['YES', 'NO'])->default('NO');
            $table->timestamps();
            $table->string('issued_date')->nullable();
            $table->integer('totalMonth')->nullable();
            $table->integer('fromYear')->nullable();
            $table->string('fromMonth')->nullable();
            $table->integer('toYear')->nullable();
            $table->string('toMonth')->nullable();
            $table->decimal('exams', 10)->default(0);
            $table->decimal('total', 10)->default(0);
            $table->decimal('idf', 10)->default(0);
            $table->decimal('tution_fee', 10)->default(0);
            $table->decimal('csf', 10)->default(0);
            $table->decimal('rdfcdf', 10)->default(0);
            $table->decimal('security_fund', 10)->default(0);
            $table->decimal('admission', 10)->default(0);
            $table->decimal('breakage', 10)->default(0);
            $table->decimal('misc', 10)->default(0);
            $table->decimal('clc', 10)->default(0);
            $table->decimal('it', 10)->default(0);
            $table->decimal('slc', 10)->default(0);
            $table->decimal('debit', 10)->default(0);
            $table->char('type', 1)->default('d');
            $table->unsignedBigInteger('school_id')->default('1');
            $table->string('session')->default('March 2024 to March 2025');
            $table->string('due_date')->nullable();
            $table->string('month_name')->nullable();
            $table->string('grno')->nullable();
            $table->decimal('total_fee', 10)->default(0);

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
        Schema::dropIfExists('challans');
    }
}
