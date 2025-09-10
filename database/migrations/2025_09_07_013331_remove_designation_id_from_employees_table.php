<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropColumn('designation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only add back designation_id if designations table exists and column doesn't exist
        if (Schema::hasTable('designations') && !Schema::hasColumn('employees', 'designation_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('designation_id')->references('designation_id')->on('designations')->cascadeOnDelete();
            });
        }
    }
};
