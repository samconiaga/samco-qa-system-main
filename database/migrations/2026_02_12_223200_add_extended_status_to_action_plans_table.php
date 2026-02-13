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
        DB::statement("
            ALTER TABLE action_plans
            DROP CONSTRAINT IF EXISTS action_plans_status_check
        ");

        DB::statement("
            ALTER TABLE action_plans
            ADD CONSTRAINT action_plans_status_check
            CHECK (status IN ('Open', 'Close', 'Overdue','Request Overdue','On Process','Submitted','Pending','Extended'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE action_plans
            DROP CONSTRAINT IF EXISTS action_plans_status_check
        ");

        DB::statement("
            ALTER TABLE action_plans
            ADD CONSTRAINT action_plans_status_check
            CHECK (status IN ('Open', 'Close', 'Overdue','Request Overdue','On Process','Submitted','Pending'))
        ");
    }
};
