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
        Schema::create('change_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('request_number')->nullable()->unique();
            $table->date('requested_date')->useCurrent();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->uuid('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->string('title');
            $table->string('initiator_name');
            $table->longText('current_status');
            $table->string('current_status_file')->nullable();
            $table->longText('proposed_change');
            $table->string('proposed_change_file')->nullable();
            $table->longText('reason');
            $table->date('expected_completion_date');
            $table->enum('overall_status', ['Pending', 'In Progress', 'Approved', 'Rejected', 'Closed'])->default('Pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
