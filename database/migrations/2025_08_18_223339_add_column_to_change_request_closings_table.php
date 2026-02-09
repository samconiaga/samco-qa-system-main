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
        Schema::table('change_request_closings', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['closed_by']);
            $table->date('pic_sign_date')->nullable();
            $table->date('qa_manager_sign_date')->nullable();
            $table->date('plant_manager_sign_date')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('change_request_closings', function (Blueprint $table) {
            $table->dropColumn(['pic_sign_date', 'qa_manager_sign_date', 'plant_manager_sign_date']);
        });
    }
};
