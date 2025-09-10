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
        Schema::table('claim_references', function (Blueprint $table) {
            if (Schema::hasColumn('claim_references', 'is_rejected')) {
                $table->dropColumn('is_rejected');
            }
            if (Schema::hasColumn('claim_references', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_references', function (Blueprint $table) {
            $table->boolean('is_rejected')->default(false)->after('receipt_path');
            $table->text('rejection_reason')->nullable()->after('is_rejected');
        });
    }
};
