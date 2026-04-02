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
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('assigned_date')->nullable()->after('assigned_to');
            $table->foreignId('assigned_by')->nullable()->after('assigned_date')->constrained('users')->nullOnDelete();
            $table->string('sla_level')->nullable()->after('assigned_by');
            $table->dateTime('estimated_delivery_date')->nullable()->after('sla_level');
            $table->dateTime('actual_delivery_date')->nullable()->after('estimated_delivery_date');
            $table->dateTime('qc_test_date')->nullable()->after('actual_delivery_date');
        });

        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'assigned_developer_id')) {
                $table->dropConstrainedForeignId('assigned_developer_id');
            }
            if (Schema::hasColumn('tickets', 'assigned_date')) {
                $table->dropColumn('assigned_date');
            }
            if (Schema::hasColumn('tickets', 'assigned_by')) {
                $table->dropConstrainedForeignId('assigned_by');
            }
            if (Schema::hasColumn('tickets', 'sla_level')) {
                $table->dropColumn('sla_level');
            }
            if (Schema::hasColumn('tickets', 'estimated_delivery_date')) {
                $table->dropColumn('estimated_delivery_date');
            }
            if (Schema::hasColumn('tickets', 'actual_delivery_date')) {
                $table->dropColumn('actual_delivery_date');
            }
            if (Schema::hasColumn('tickets', 'qc_test_date')) {
                $table->dropColumn('qc_test_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('assigned_developer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_date')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sla_level')->nullable();
            $table->dateTime('estimated_delivery_date')->nullable();
            $table->dateTime('actual_delivery_date')->nullable();
            $table->dateTime('qc_test_date')->nullable();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('assigned_date');
            $table->dropConstrainedForeignId('assigned_by');
            $table->dropColumn([
                'sla_level',
                'estimated_delivery_date',
                'actual_delivery_date',
                'qc_test_date'
            ]);
        });
    }
};
