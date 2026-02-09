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
        DB::table('regulatory_assesments')
            ->whereNotIn('halal_status', ['Yes BPJPH Required', 'Yes No BPJH', 'No'])
            ->update(['halal_status' => 'No']);

        // 2. Drop constraint lama
        DB::statement("
            ALTER TABLE regulatory_assesments
            DROP CONSTRAINT IF EXISTS regulatory_assesments_halal_status_check
        ");

        // 3. Tambahkan constraint baru
        DB::statement("
            ALTER TABLE regulatory_assesments
            ADD CONSTRAINT regulatory_assesments_halal_status_check
            CHECK (halal_status IN ('Yes BPJPH Required', 'Yes No BPJH', 'No'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus constraint baru
        DB::statement("
            ALTER TABLE regulatory_assesments
            DROP CONSTRAINT IF EXISTS regulatory_assesments_halal_status_check
        ");

        // Buat constraint lama kembali
        DB::statement("
            ALTER TABLE regulatory_assesments
            ADD CONSTRAINT regulatory_assesments_halal_status_check
            CHECK (halal_status IN ('Yes BPJPH Required', 'Yes No BPJH', 'No'))
        ");
    }
};
