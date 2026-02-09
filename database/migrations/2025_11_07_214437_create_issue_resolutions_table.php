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
        Schema::create('issue_resolutions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')
                ->constrained('issues')
                ->cascadeOnDelete();
            $table->longText('resolution_description');
            $table->longText('gap_analysis');
            $table->longText('root_cause_analysis');
            $table->longText('corrective_action');
            $table->longText('preventive_action');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_resolutions');
    }
};
