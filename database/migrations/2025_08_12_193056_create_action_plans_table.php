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
        Schema::create('action_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('change_request_id');
            $table->foreign('change_request_id')->references('id')->on('change_requests')->onDelete('cascade');
            $table->text('action_proposal')->nullable();
            $table->uuid('pic_id')->nullable();
            $table->foreign('pic_id')->references('id')->on('employees')->onDelete('cascade');
            $table->uuid('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->date('timeline')->nullable();
            $table->string('completion_proof_file')->nullable();
            $table->enum('status', ['Open', 'Overdue', 'Closed'])->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_plans');
    }
};
