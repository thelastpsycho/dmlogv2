<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable strict SQL mode temporarily for legacy table compatibility
        DB::statement('SET SQL_MODE=""');

        // Add priority column if not exists
        if (!Schema::hasColumn('issues', 'priority')) {
            DB::statement("ALTER TABLE issues ADD COLUMN priority VARCHAR(255) DEFAULT 'medium' NULL AFTER training");
        }

        // Add assigned_to_user_id column if not exists
        if (!Schema::hasColumn('issues', 'assigned_to_user_id')) {
            DB::statement("ALTER TABLE issues ADD COLUMN assigned_to_user_id INT UNSIGNED NULL AFTER priority");
        }

        // Add closed_at column if not exists
        if (!Schema::hasColumn('issues', 'closed_at')) {
            DB::statement("ALTER TABLE issues ADD COLUMN closed_at TIMESTAMP NULL AFTER assigned_to_user_id");
        }

        // Add closed_by_user_id column if not exists
        if (!Schema::hasColumn('issues', 'closed_by_user_id')) {
            DB::statement("ALTER TABLE issues ADD COLUMN closed_by_user_id INT UNSIGNED NULL AFTER closed_at");
        }

        // Add deleted_at for soft deletes if not exists
        if (!Schema::hasColumn('issues', 'deleted_at')) {
            DB::statement("ALTER TABLE issues ADD COLUMN deleted_at TIMESTAMP NULL AFTER updated_at");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('issues', 'priority')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }
        if (Schema::hasColumn('issues', 'assigned_to_user_id')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropColumn('assigned_to_user_id');
            });
        }
        if (Schema::hasColumn('issues', 'closed_at')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropColumn('closed_at');
            });
        }
        if (Schema::hasColumn('issues', 'closed_by_user_id')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropColumn('closed_by_user_id');
            });
        }
        if (Schema::hasColumn('issues', 'deleted_at')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
};
