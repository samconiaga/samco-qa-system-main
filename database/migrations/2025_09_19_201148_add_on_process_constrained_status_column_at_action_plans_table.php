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
        // 1. Drop constraint lama
        DB::statement("
            ALTER TABLE action_plans
            DROP CONSTRAINT IF EXISTS action_plans_status_check
        ");

        // 2. Tambahkan constraint baru
        DB::statement("
            ALTER TABLE action_plans
            ADD CONSTRAINT action_plans_status_check
            CHECK (status IN ('Open', 'Close', 'Overdue','Request Overdue','On Process'))
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus constraint baru
        DB::statement("
            ALTER TABLE action_plans
            DROP CONSTRAINT IF EXISTS action_plans_status_check
        ");

        // Buat constraint lama kembali
        DB::statement("
            ALTER TABLE action_plans
            ADD CONSTRAINT action_plans_status_check
            CHECK (status IN ('Open', 'Close', 'Overdue','Request Overdue'))");
    }
};
