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
        Schema::create('claim_references', function (Blueprint $table) {
            $table->id('claim_reference_id');
            $table->foreignId('claim_id')->nullable()->references('claim_id')->on('claims')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->references('category_id')->on('categories')->nullOnDelete();
            $table->string('description');
            $table->date('expense_date');
            $table->decimal('amount', 15, 2);
            $table->string('receipt_path')->nullable(); // upload path for receipt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_references');
    }
};
