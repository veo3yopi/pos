<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['cash', 'transfer']);
            $table->decimal('amount', 15, 2);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['transaction_id', 'method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
