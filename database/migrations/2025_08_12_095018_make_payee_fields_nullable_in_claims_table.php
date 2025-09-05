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
        Schema::table('claims', function (Blueprint $table) {
            // Make payee fields nullable so we can create claims without payee info initially
            $table->string('payee_type')->nullable()->change();
            $table->unsignedBigInteger('payee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // Revert back to not nullable
            $table->string('payee_type')->nullable(false)->change();
            $table->unsignedBigInteger('payee_id')->nullable(false)->change();
        });
    }
};
