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
        Schema::create('regulatory_assesments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('change_request_id');
            $table->foreign('change_request_id')->references('id')->on('change_requests')->onDelete('cascade');
            $table->enum('regulatory_related', ['Yes', 'No'])->nullable();
            $table->text('regulatory_notes')->nullable();
            $table->enum('halal_status', ['Yes BPJPH Required', 'Yes No BPJPH', 'No'])->nullable();
            $table->text('halal_notes')->nullable();
            $table->boolean('third_party_involved')->default(false)->nullable();
            $table->string('third_party_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulatory_assesments');
    }
};
