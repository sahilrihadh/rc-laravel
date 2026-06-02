<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_name');
            $table->string('state_name');
            $table->string('pincode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['city_name', 'state_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};