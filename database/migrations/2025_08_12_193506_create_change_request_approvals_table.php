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
        Schema::create('change_request_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('change_request_id');
            $table->foreign('change_request_id')->references('id')->on('change_requests')->onDelete('cascade');
            $table->string('stage'); // manager_approval, qa_spv_review, qa_manager_approval, plant_manager_approval
            $table->uuid('approver_id');
            $table->foreign('approver_id')->references('id')->on('employees')->onDelete('cascade');
            $table->enum('decision', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('comments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_request_approvals');
    }
};
