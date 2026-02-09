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
        Schema::create('change_request_closings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('change_request_id');
            $table->foreign('change_request_id')->references('id')->on('change_requests')->onDelete('cascade');
            $table->enum('verification_documents_complete', ['Sudah', 'Belum', 'NA']);
            $table->enum('verification_actions_done', ['Sudah', 'Belum', 'NA']);
            $table->enum('verification_regulatory_approval', ['Sudah', 'Belum', 'NA']);
            $table->uuid('closed_by');
            $table->foreign('closed_by')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_request_closings');
    }
};
