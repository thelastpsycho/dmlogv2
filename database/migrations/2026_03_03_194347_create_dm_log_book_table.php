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
        if (!Schema::hasTable('dm_log_book')) {
            Schema::create('dm_log_book', function (Blueprint $table) {
                $table->id();
                $table->string('guest_name');
                $table->string('room_number')->nullable();
                $table->string('category')->index();
                $table->text('description');
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->index();
                $table->string('assigned_to')->nullable();
                $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open')->index();
                $table->text('action_taken')->nullable();
                $table->string('created_by');
                $table->timestamp('created_at')->index();
                $table->timestamp('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dm_log_book');
    }
};
