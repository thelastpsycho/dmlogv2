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
        if (!Schema::hasTable('issues')) {
            Schema::create('issues', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('name')->nullable(); // Guest name
                $table->string('room_number')->nullable();
                $table->date('checkin_date')->nullable();
                $table->date('checkout_date')->nullable();
                $table->date('issue_date')->nullable();
                $table->string('source')->nullable();
                $table->string('nationality')->nullable();
                $table->string('contact')->nullable();
                $table->text('recovery')->nullable();
                $table->unsignedInteger('recovery_cost')->nullable();
                $table->string('training')->nullable();
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
                $table->string('status')->default('open');
                $table->string('created_by');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->foreignId('assigned_to_user_id')->nullable()->constrained('users');
                $table->timestamp('closed_at')->nullable();
                $table->foreignId('closed_by_user_id')->nullable()->constrained('users');
                $table->softDeletes();
                $table->timestamps();

                $table->index(['status', 'created_at']);
                $table->index('priority');
                $table->index('assigned_to_user_id');
                $table->index('closed_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
