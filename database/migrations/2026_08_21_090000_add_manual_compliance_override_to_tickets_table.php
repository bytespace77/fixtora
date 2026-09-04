<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tickets', 'compliance_manually_overridden')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->boolean('compliance_manually_overridden')
                    ->default(false)
                    ->after('penalty_points');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'compliance_manually_overridden')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('compliance_manually_overridden');
            });
        }
    }
};
