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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('assigned_developer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_date')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sla_level')->nullable();
            $table->dateTime('estimated_delivery_date')->nullable();
            $table->dateTime('actual_delivery_date')->nullable();
            $table->dateTime('qc_test_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_developer_id');
            $table->dropColumn('assigned_date');
            $table->dropConstrainedForeignId('assigned_by');
            $table->dropColumn('sla_level');
            $table->dropColumn('estimated_delivery_date');
            $table->dropColumn('actual_delivery_date');
            $table->dropColumn('qc_test_date');
        });
    }
};
