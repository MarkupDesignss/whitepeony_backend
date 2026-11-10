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
         Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['standard shipping', 'express shipping','free shipping'])->default('standard shipping');
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->string('estimated_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};