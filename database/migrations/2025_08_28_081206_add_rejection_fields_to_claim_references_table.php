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
            $table->boolean('rejected')->default(false)->after('receipt_path');
            $table->text('reason')->nullable()->after('rejected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_references', function (Blueprint $table) {
            $table->dropColumn(['rejected', 'reason']);
        });
    }
};
