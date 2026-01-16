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
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->enum('status', ['paid', 'void'])->default('paid');
            $table->timestamps();

            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
