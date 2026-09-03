<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedInteger('sla_limit_hours')->nullable()->after('sla_level');
            $table->dateTime('sla_due_at')->nullable()->after('sla_limit_hours');
            $table->dateTime('resolved_at')->nullable()->after('actual_delivery_date');
            $table->unsignedBigInteger('resolution_minutes')->nullable()->after('resolved_at');
            $table->string('compliance_status', 20)->nullable()->after('resolution_minutes');
            $table->unsignedInteger('penalty_points')->default(0)->after('compliance_status');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'sla_limit_hours', 'sla_due_at', 'resolved_at',
                'resolution_minutes', 'compliance_status', 'penalty_points',
            ]);
        });
    }
};
