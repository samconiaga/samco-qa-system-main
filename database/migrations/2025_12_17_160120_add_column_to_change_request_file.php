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
        Schema::table('change_request_supporting_attachments', function (Blueprint $table) {
            $table->text('original_name')->nullable()->after('file_path');
        });
        Schema::table('current_status_files', function (Blueprint $table) {
            $table->text('original_name')->nullable()->after('file_path');
        });
        Schema::table('proposed_change_files', function (Blueprint $table) {
            $table->text('original_name')->nullable()->after('file_path');
        });
        Schema::table('change_requests', function (Blueprint $table) {
            $table->string('conclusion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_request_supporting_attachments', function (Blueprint $table) {
            $table->dropColumn('original_name');
        });

        Schema::table('current_status_files', function (Blueprint $table) {
            $table->dropColumn('original_name');
        });

        Schema::table('proposed_change_files', function (Blueprint $table) {
            $table->dropColumn('original_name');
        });

        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn('conclusion');
        });
    }
};
