<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE change_requests
            DROP CONSTRAINT IF EXISTS change_requests_overall_status_check
        ");

        DB::statement("
            ALTER TABLE change_requests
            ADD CONSTRAINT change_requests_overall_status_check
            CHECK (overall_status IN ('Pending','Waiting SPV Approval','Draft','In Progress','Reviewed' ,'Approved', 'Rejected','Waiting Close','Closed'))
        ");


        DB::statement("
            ALTER TABLE change_requests
            ALTER COLUMN overall_status SET DEFAULT 'Pending'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE change_requests
            DROP CONSTRAINT IF EXISTS change_requests_overall_status_check
        ");

        DB::statement("
            ALTER TABLE change_requests
            ADD CONSTRAINT change_requests_overall_status_check
            CHECK (overall_status IN (
                'Pending',
                'In Progress',
                'Reviewed',
                'Approved',
                'Rejected',
                'Closed'
            ))
        ");

        DB::statement("
            ALTER TABLE change_requests
            ALTER COLUMN overall_status SET DEFAULT 'Pending'
        ");
    }
};
