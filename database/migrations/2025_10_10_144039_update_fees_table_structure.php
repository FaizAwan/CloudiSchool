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
        Schema::table('fees', function (Blueprint $table) {
            // Add new columns that the controller expects
            $table->string('class_name')->nullable()->after('class_id');
            $table->string('month')->nullable();
            $table->string('month_name')->nullable();
            $table->integer('year')->nullable();
            $table->unsignedBigInteger('fee_type_id')->nullable();
            $table->decimal('fee_value', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn(['class_name', 'month', 'month_name', 'year', 'fee_type_id', 'fee_value']);
        });
    }
};
