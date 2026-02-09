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
        Schema::create('issue_approval_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')
                ->constrained('issues')
                ->cascadeOnDelete();
            $table->enum('status', ['Open', 'In Progress', 'Submitted', 'Resolved', 'Rejected']);
            $table->string('comment')->nullable();
            $table->foreignUuid('approver_id')
                ->constrained('employees')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_approval_histories');
    }
};
