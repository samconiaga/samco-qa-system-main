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
        // 1. Update data lama yang tidak valid
        DB::table('change_requests')
            ->whereNotIn('overall_status', ['Pending', 'In Progress', 'Reviewed', 'Approved', 'Rejected', 'Closed'])
            ->update(['overall_status' => 'Pending']);

        // 2. Drop constraint lama
        DB::statement("
            ALTER TABLE change_requests
            DROP CONSTRAINT IF EXISTS change_requests_overall_status_check
        ");

        // 3. Tambahkan constraint baru
        DB::statement("
            ALTER TABLE change_requests
            ADD CONSTRAINT change_requests_overall_status_check
            CHECK (overall_status IN ('Pending','In Progress','Reviewed','Approved','Rejected','Closed'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus constraint baru
        DB::statement("
            ALTER TABLE change_requests
            DROP CONSTRAINT IF EXISTS change_requests_overall_status_check
        ");

        // Buat constraint lama kembali
        DB::statement("
            ALTER TABLE change_requests
            ADD CONSTRAINT change_requests_overall_status_check
            CHECK (overall_status IN ('Pending','','Approved','Rejected','Closed'))
        ");
    }
};
