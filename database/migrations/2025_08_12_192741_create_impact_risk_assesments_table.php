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
        Schema::create('impact_risk_assesments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('change_request_id');
            $table->foreign('change_request_id')->references('id')->on('change_requests')->onDelete('cascade');
            $table->text('source_of_risk');
            $table->text('impact_of_risk');
            $table->integer('severity');
            $table->text('cause_of_risk');
            $table->integer('probability');
            $table->text('control_implemented');
            $table->integer('detectability');
            $table->integer('rpn');
            $table->string('risk_category');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impact_risk_assesments');
    }
};
