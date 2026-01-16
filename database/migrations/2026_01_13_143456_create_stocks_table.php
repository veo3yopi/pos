<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->integer('qty');
            $table->timestamps();

            $table->unique('variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
