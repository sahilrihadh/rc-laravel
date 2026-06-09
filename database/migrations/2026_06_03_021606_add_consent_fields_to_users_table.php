<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the 3 consent fields only
            if (!Schema::hasColumn('users', 'terms_accepted')) {
                $table->boolean('terms_accepted')->default(false)->after('state');
            }

            if (!Schema::hasColumn('users', 'sale_consent')) {
                $table->boolean('sale_consent')->default(false)->after('terms_accepted');
            }

            if (!Schema::hasColumn('users', 'research_consent')) {
                $table->boolean('research_consent')->default(false)->after('sale_consent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['terms_accepted', 'sale_consent', 'research_consent'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
