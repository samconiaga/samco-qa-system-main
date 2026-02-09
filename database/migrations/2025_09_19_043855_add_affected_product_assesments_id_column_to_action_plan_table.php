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
        Schema::table('action_plans', function (Blueprint $table) {
            $table->uuid('affected_product_assesment_detail_id')->nullable()->after('change_request_id');
            $table->foreign('affected_product_assesment_detail_id')->references('id')->on('affected_product_assesment_details')->onDelete('cascade');
            $table->dropForeign(['pic_id']);
            $table->dropColumn('pic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_plans', function (Blueprint $table) {
            $table->dropColumn('affected_product_assesment_detail_id');
            $table->uuid('pic_id')->nullable()->after('action_proposal');
            $table->foreign('pic_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }
};
