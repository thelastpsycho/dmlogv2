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
        Schema::table('issues', function (Blueprint $table) {
            // Add verified_by_user_id foreign key
            $table->foreignId('verified_by_user_id')->nullable()->after('closed_by_user_id')->constrained('users');
            // Add verified_at timestamp
            $table->timestamp('verified_at')->nullable()->after('verified_by_user_id');
            // Add index for verified_at to match the pattern of closed_at
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            // Drop the index first
            $table->dropIndex(['verified_at']);
            // Drop the foreign key constraint and column
            $table->dropForeign(['verified_by_user_id']);
            $table->dropColumn(['verified_by_user_id', 'verified_at']);
        });
    }
};
