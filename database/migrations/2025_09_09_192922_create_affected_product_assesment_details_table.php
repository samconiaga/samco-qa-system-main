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
        Schema::create('affected_product_assesment_details', function (Blueprint $table) {
            $table->uuid('id')->primary()->index();
            $table->uuid('affected_product_assesment_id');
            $table->foreign('affected_product_assesment_id')->references('id')->on('affected_product_assesments')->onDelete('cascade');
            $table->string('impact_of_change_category')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affected_product_assesment_details');
    }
};
