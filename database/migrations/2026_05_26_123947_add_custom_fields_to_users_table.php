<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->after('id');
            $table->string('mobile_number')->unique()->after('full_name');
            $table->string('email_id')->unique()->after('mobile_number');
            $table->string('clinic_name')->nullable()->after('email_id');
            $table->string('registration_number')->nullable()->after('clinic_name');
            $table->string('city')->nullable()->after('registration_number');
            $table->string('state')->nullable()->after('city');
            $table->string('city_pincode')->nullable()->after('state');
            $table->timestamp('registered_at')->useCurrent()->after('city_pincode');
            $table->timestamp('last_seen_at')->nullable()->after('remember_token');
            $table->boolean('is_online')->default(false)->after('last_seen_at');
            $table->boolean('is_admin')->default(false)->after('is_online');
            
            // Remove password column since we're using email login without password
            $table->dropColumn('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'mobile_number',
                'email_id',
                'clinic_name',
                'registration_number',
                'city',
                'state',
                'city_pincode',
                'registered_at',
                'last_seen_at',
                'is_online',
                'is_admin'
            ]);
            $table->string('password')->nullable();
        });
    }
};