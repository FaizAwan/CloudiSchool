<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('principal_remarks')) {
            Schema::create('principal_remarks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
                $table->decimal('percentage_min', 5, 2)->default(0);
                $table->decimal('percentage_max', 5, 2)->default(100);
                $table->text('remark');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['is_active', 'sort_order', 'tenant_id']);
                $table->index(['percentage_min', 'percentage_max']);
                
                // Add FK if schools table exists
                if (Schema::hasTable('schools')) {
                    $table->foreign('tenant_id')->references('id')->on('schools')->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('principal_remarks');
    }
};