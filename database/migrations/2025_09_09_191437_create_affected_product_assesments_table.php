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
        Schema::create('affected_product_assesments', function (Blueprint $table) {
            $table->uuid('id')->primary()->index();
            $table->uuid('change_request_id');
            $table->foreign('change_request_id')->references('id')->on('change_requests')->onDelete('cascade');
            $table->uuid('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->enum('evaluation_status', ['Approved', 'Approved Not Affected', 'Rejected'])->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affected_product_assesments');
    }
};
        