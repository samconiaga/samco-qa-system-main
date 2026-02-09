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
        Schema::table('regulatory_assesments', function (Blueprint $table) {
            $table->longText('change_type')->nullable()->after('third_party_name');
            $table->string('reported_by')->nullable()->after('change_type');
            $table->date('notification_date')->nullable()->after('reported_by');
            $table->date('approved_at')->nullable()->after('notification_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulatory_assesments', function (Blueprint $table) {
            $table->dropColumn('change_type');
            $table->dropColumn('reported_by');
            $table->dropColumn('notification_date');
            $table->dropColumn('approved_at');
        });
    }
};
