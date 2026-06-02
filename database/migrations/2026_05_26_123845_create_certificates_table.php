<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('webinar_session_id')->constrained()->onDelete('cascade');
            $table->string('certificate_code')->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'webinar_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};