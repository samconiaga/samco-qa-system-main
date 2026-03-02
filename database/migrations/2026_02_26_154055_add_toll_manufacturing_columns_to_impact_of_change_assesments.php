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
        Schema::table('impact_of_change_assesments', function (Blueprint $table) {
            $table->boolean('is_informed_to_toll_manufacturing')->nullable();
            $table->boolean('is_approval_required_from_toll_manufacturing')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('impact_of_change_assesments', function (Blueprint $table) {
            $table->dropColumn(['is_informed_to_toll_manufacturing', 'is_approval_required_from_toll_manufacturing']);
        });
    }
};
