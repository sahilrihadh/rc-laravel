<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('previous_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email_id');
            $table->string('session_name'); // webinar1, webinar2, etc.
            $table->timestamp('watched_on')->nullable();
            $table->boolean('certificate_status')->default(0); // 0 = pending, 1 = sent
            $table->string('certificate_path')->nullable();
            $table->integer('count')->default(1);
            $table->timestamps();

            // Add index for faster queries
            $table->index(['email_id', 'session_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('previous_sessions');
    }
};
