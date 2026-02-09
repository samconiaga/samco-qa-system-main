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
        Schema::create('completion_proof_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('action_plan_id')->constrained('action_plans')
                ->cascadeOnDelete();
            $table->string('file_path');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('action_plans', function (Blueprint $table) {
            $table->dropColumn('completion_proof_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completion_proof_files');
        Schema::table('action_plans', function (Blueprint $table) {
            $table->string('completion_proof_file')->nullable();
        });
    }
};
