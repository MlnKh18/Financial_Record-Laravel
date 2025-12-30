<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('source_id')->constrained();

            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('transaction_date');

            $table->timestamp('approved_at')->nullable(); // ✅ INI YANG BENAR
            $table->timestamps();                          // created_at, updated_at
            $table->softDeletes();                         // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
