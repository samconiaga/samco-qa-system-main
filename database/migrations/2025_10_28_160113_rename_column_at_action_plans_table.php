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
            $table->renameColumn('affected_product_assesment_detail_id', 'follow_up_implementation_detail_id');
            $table->renameColumn('timeline', 'deadline');
            $table->renameColumn('action_proposal', 'realization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_plans', function (Blueprint $table) {
            $table->renameColumn('follow_up_implementation_detail_id', 'affected_product_assesment_detail_id');
            $table->renameColumn('deadline', 'timeline');
            $table->renameColumn('realization', 'action_proposal');
        });
    }
};
