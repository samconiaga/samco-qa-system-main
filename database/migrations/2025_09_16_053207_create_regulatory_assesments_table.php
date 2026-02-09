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
            $table->string('regulatory_change_type')->nullable();
            $table->string('regulatory_variation')->nullable();
            $table->string('reported_by')->nullable();
            $table->date('notification_date')->nullable();
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
