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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 50);
            $table->string('business_name')->nullable();
            $table->integer('vat_id');
            $table->string('otp', 50)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('customer_type', ['hotel', 'restaurant', 'cafe']);
            $table->enum('tier_id', ['1', '2', '3','4']);
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->timestamp('otp_created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};