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
        Schema::table('challans', function (Blueprint $table) {
            // Add missing fields that the controller expects
            $table->string('student_name')->nullable()->after('student_id');
            $table->string('month')->nullable()->after('class_name');
            $table->integer('year')->nullable()->after('month');
            $table->enum('paid', ['YES', 'NO'])->default('NO')->after('status');
            $table->string('issued_date')->nullable();
            $table->integer('totalMonth')->nullable();
            $table->integer('fromYear')->nullable();
            $table->string('fromMonth')->nullable();
            $table->integer('toYear')->nullable();
            $table->string('toMonth')->nullable();
            $table->decimal('exams', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('idf', 10, 2)->default(0);
            $table->decimal('tution_fee', 10, 2)->default(0);
            $table->decimal('csf', 10, 2)->default(0);
            $table->decimal('rdfcdf', 10, 2)->default(0);
            $table->decimal('security_fund', 10, 2)->default(0);
            $table->decimal('admission', 10, 2)->default(0);
            $table->decimal('breakage', 10, 2)->default(0);
            $table->decimal('misc', 10, 2)->default(0);
            $table->decimal('clc', 10, 2)->default(0);
            $table->decimal('it', 10, 2)->default(0);
            $table->decimal('slc', 10, 2)->default(0);
            $table->decimal('debit', 10, 2)->default(0);
            $table->char('type', 1)->default('d'); // 'd' for debit, 'c' for credit
            $table->unsignedBigInteger('school_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn([
                'student_name', 'month', 'year', 'paid', 'issued_date', 'totalMonth',
                'fromYear', 'fromMonth', 'toYear', 'toMonth', 'exams', 'total',
                'idf', 'tution_fee', 'csf', 'rdfcdf', 'security_fund', 'admission',
                'breakage', 'misc', 'clc', 'it', 'slc', 'debit', 'type', 'school_id'
            ]);
        });
    }
};
