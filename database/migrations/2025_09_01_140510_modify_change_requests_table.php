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
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn(['current_status_file']);
            $table->dropColumn(['proposed_change_file']);
            $table->dropColumn(['expected_completion_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->string('current_status_file')->nullable();
            $table->string('proposed_change_file')->nullable();
            $table->date('expected_completion_date')->nullable();
        });
    }
};
