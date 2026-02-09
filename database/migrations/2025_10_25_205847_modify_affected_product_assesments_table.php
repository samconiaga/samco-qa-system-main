<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1️⃣ Hapus constraint lama (kalau ada)
        DB::statement("
            ALTER TABLE affected_product_assesments
            DROP CONSTRAINT IF EXISTS affected_product_assesments_evaluation_status_check
        ");

        // 2️⃣ Tambahkan constraint baru (isi enum terbaru)
        DB::statement("
            ALTER TABLE affected_product_assesments
            ADD CONSTRAINT affected_product_assesments_evaluation_status_check
            CHECK (evaluation_status IN ('Agree', 'Agree Not Impacted', 'Disagree' ))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus constraint baru
        DB::statement("
            ALTER TABLE affected_product_assesments
            DROP CONSTRAINT IF EXISTS affected_product_assesments_evaluation_status_check
        ");

        // Balik ke constraint lama
        DB::statement("
            ALTER TABLE affected_product_assesments
            ADD CONSTRAINT affected_product_assesments_evaluation_status_check
            CHECK (evaluation_status IN ('Approved', 'Approved Not Affected', 'Rejected'))
        ");
    }
};
