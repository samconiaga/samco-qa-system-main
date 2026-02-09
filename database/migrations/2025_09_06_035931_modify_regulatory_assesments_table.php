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
        Schema::rename('regulatory_assesments', 'impact_of_change_assesments');
        Schema::table('impact_of_change_assesments', function (Blueprint $table) {
            $table->enum('facility_affected', ['Yes, BPOM Notification Required', 'Yes After BPOM Notification', 'No, Facility Not Affected', 'No'])->after('third_party_name')->nullable();
            $table->enum('product_affected', ['Yes', 'No'])->after('facility_affected')->nullable();
            $table->dropColumn('halal_notes');
            $table->dropColumn('regulatory_notes');
            $table->dropColumn('change_type');
            $table->dropColumn('reported_by');
            $table->dropColumn('notification_date');
            $table->dropColumn('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('impact_of_change_assesments', function (Blueprint $table) {
            $table->dropColumn(['facility_affected', 'product_affected']);
            $table->text('halal_notes')->nullable()->after('halal_status');
            $table->text('regulatory_notes')->nullable()->after('regulatory_related');
            $table->longText('change_type')->nullable()->after('third_party_name');
            $table->string('reported_by')->nullable()->after('change_type');
            $table->date('notification_date')->nullable()->after('reported_by');
            $table->date('approved_at')->nullable()->after('notification_date');
        });
        Schema::rename('impact_of_change_assesments', 'regulatory_assesments');
    }
};
