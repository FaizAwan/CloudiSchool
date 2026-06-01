<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('subject_code');
            }
            if (!Schema::hasColumn('subjects', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('passing_marks');
            }
            if (!Schema::hasColumn('subjects', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('status');
            }
            if (!Schema::hasColumn('subjects', 'term')) {
                $table->string('term', 50)->nullable()->after('class_id');
            }
            if (!Schema::hasColumn('subjects', 'term_marks')) {
                $table->json('term_marks')->nullable()->after('passing_marks');
            }
            // Helpful indexes
            try { $table->index(['class_id', 'status']); } catch (\Throwable $e) {}
            try { $table->index(['class_id', 'term', 'status'], 'subjects_class_term_status_index'); } catch (\Throwable $e) {}
            try { $table->index(['class_id', 'sort_order'], 'subjects_class_sort_idx'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Non-destructive down: keep columns to avoid breaking existing features
        });
    }
};
