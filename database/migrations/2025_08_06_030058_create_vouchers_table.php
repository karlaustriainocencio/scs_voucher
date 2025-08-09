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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id('voucher_id');
            $table->string('voucher_number')->unique(); // e.g. V2508-001
            $table->foreignId('claim_id')->nullable()->unique()->references('claim_id')->on('claims')->cascadeOnDelete(); // one voucher per claim
            $table->foreignId('mode_of_payment_id')->nullable()->references('mode_of_payment_id')->on('mode_of_payments')->cascadeOnDelete();
            $table->date('payment_date');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->references('user_id')->on('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->references('user_id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
