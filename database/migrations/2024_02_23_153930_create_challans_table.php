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
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('class_name')->nullable();
            $table->string('challan_number')->unique()->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['generated', 'paid', 'cancelled'])->default('generated');
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index('challan_number');
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
