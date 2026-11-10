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
         Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('business_name');
            $table->text('business_address');
            $table->string('vat_number', 100);
            $table->string('contact_number', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};