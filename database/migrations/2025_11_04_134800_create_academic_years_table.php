<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->enum('is_active', ['yes','no','closed'])->default('no');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
