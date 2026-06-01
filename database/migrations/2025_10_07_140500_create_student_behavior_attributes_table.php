<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_behavior_attributes')) {
            Schema::create('student_behavior_attributes', function (Blueprint $table) {
                $table->id();
                $table->integer('student_id');
                $table->integer('class_id');
                $table->string('section', 50)->nullable();
                $table->string('session', 128)->nullable();
                $table->string('term', 64);
                $table->json('attributes');
                $table->decimal('overall_average', 5, 2)->nullable();
                $table->timestamps();

                $table->unique(['student_id','class_id','section','session','term'], 'uniq_student_behavior_scope');
                $table->index(['class_id','section','term'], 'idx_class_section_term');
            });
        } else {
            if (!Schema::hasColumn('student_behavior_attributes', 'overall_average')) {
                Schema::table('student_behavior_attributes', function (Blueprint $table) {
                    $table->decimal('overall_average', 5, 2)->nullable()->after('attributes');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_behavior_attributes');
    }
};
