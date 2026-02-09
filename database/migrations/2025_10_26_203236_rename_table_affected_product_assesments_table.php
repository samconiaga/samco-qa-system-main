<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi rename tabel & relasi.
     */
    public function up(): void
    {
      
        Schema::table('affected_product_assesment_details', function (Blueprint $table) {
            $table->dropForeign(['affected_product_assesment_id']);
        });

        if (Schema::hasTable('affected_product_assesments')) {
            Schema::rename('affected_product_assesments', 'follow_up_implementations');
            Schema::table('follow_up_implementations', function (Blueprint $table) {
                $table->uuid('assesment_by')->nullable()->after('evaluation_status');
                $table->foreign('assesment_by')->references('id')->on('employees')->onDelete('set null');
            });
        }

        if (Schema::hasTable('affected_product_assesment_details')) {
            Schema::rename('affected_product_assesment_details', 'follow_up_implementation_details');
        }

      
        if (
            Schema::hasTable('follow_up_implementation_details') &&
            Schema::hasColumn('follow_up_implementation_details', 'affected_product_assesment_id')
        ) {
            DB::statement('ALTER TABLE follow_up_implementation_details RENAME COLUMN affected_product_assesment_id TO follow_up_implementation_id;');
        }

        Schema::table('follow_up_implementation_details', function (Blueprint $table) {
            $table->foreign('follow_up_implementation_id')
                ->references('id')
                ->on('follow_up_implementations')
                ->onDelete('cascade');
        });
    }

    /**
     * Rollback (balik ke nama lama).
     */
    public function down(): void
    {
        // 🔹 1. Drop FK baru
        Schema::table('follow_up_implementation_details', function (Blueprint $table) {
            $table->dropForeign(['follow_up_implementation_id']);
        });

        // 🔹 2. Rename kolom balik (pakai SQL)
        if (
            Schema::hasTable('follow_up_implementation_details') &&
            Schema::hasColumn('follow_up_implementation_details', 'follow_up_implementation_id')
        ) {
            DB::statement('ALTER TABLE follow_up_implementation_details RENAME COLUMN follow_up_implementation_id TO affected_product_assesment_id;');
        }

        // 🔹 3. Rename tabel detail balik
        if (Schema::hasTable('follow_up_implementation_details')) {
            Schema::rename('follow_up_implementation_details', 'affected_product_assesment_details');
        }

        // 🔹 4. Rename tabel utama balik
        if (Schema::hasTable('follow_up_implementations')) {
            Schema::table('follow_up_implementations', function (Blueprint $table) {
                $table->dropForeign(['assesment_by']);
                $table->dropColumn('assesment_by');
            });
            Schema::rename('follow_up_implementations', 'affected_product_assesments');
        }

        // 🔹 5. Tambahkan kembali FK lama
        Schema::table('affected_product_assesment_details', function (Blueprint $table) {
            $table->foreign('affected_product_assesment_id')
                ->references('id')
                ->on('affected_product_assesments')
                ->onDelete('cascade');
        });
    }
};
