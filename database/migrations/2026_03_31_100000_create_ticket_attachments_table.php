<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');   // e.g. my-screenshot.png
            $table->string('stored_path');      // e.g. ticket-attachments/5/abc123.png
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size'); // bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};