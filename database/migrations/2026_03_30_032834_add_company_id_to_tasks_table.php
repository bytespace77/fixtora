<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'company_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('company_id')
                      ->nullable()
                      ->after('id')
                      ->constrained()
                      ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tasks', 'company_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};