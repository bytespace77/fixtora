<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();      // creator
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // assignee
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete(); // linked ticket
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low | medium | high | urgent
            $table->string('status')->default('todo');     // todo | doing | done
            $table->integer('progress')->default(0);       // 0-100
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
