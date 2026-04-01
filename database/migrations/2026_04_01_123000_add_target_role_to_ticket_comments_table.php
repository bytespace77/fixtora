<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ticket_comments', 'target_role')) {
            Schema::table('ticket_comments', function (Blueprint $table) {
                $table->string('target_role')->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ticket_comments', 'target_role')) {
            Schema::table('ticket_comments', function (Blueprint $table) {
                $table->dropColumn('target_role');
            });
        }
    }
};
