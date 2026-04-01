<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tickets', 'assigned_developer_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreignId('assigned_developer_id')->nullable()->after('due_date')->constrained('users')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('tickets', 'assigned_date')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->timestamp('assigned_date')->nullable()->after('assigned_developer_id');
            });
        }

        if (!Schema::hasColumn('tickets', 'assigned_by')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreignId('assigned_by')->nullable()->after('assigned_date')->constrained('users')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('tickets', 'sla_level')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('sla_level')->nullable()->after('assigned_by');
            });
        }

        if (!Schema::hasColumn('tickets', 'estimated_delivery_date')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dateTime('estimated_delivery_date')->nullable()->after('sla_level');
            });
        }

        if (!Schema::hasColumn('tickets', 'actual_delivery_date')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dateTime('actual_delivery_date')->nullable()->after('estimated_delivery_date');
            });
        }

        if (!Schema::hasColumn('tickets', 'qc_test_date')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dateTime('qc_test_date')->nullable()->after('actual_delivery_date');
            });
        }
    }

    public function down(): void
    {
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
};
