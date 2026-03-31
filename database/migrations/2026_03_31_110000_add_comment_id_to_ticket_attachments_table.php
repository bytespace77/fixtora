<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            // nullable — ticket-level attachments have no comment_id
            // comment attachments have a comment_id
            $table->foreignId('comment_id')
                  ->nullable()
                  ->after('ticket_id')
                  ->constrained('ticket_comments')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->dropForeign(['comment_id']);
            $table->dropColumn('comment_id');
        });
    }
};