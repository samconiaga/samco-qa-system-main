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
        Schema::create('issues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('issue_number')->unique()->nullable();
            $table->foreignUuid('capa_type_id')
                ->nullable()
                ->constrained('capa_types')
                ->nullOnDelete();
            $table->foreignUuid('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();
            $table->string('subject');
            $table->longText('finding');
            $table->enum('criteria', ['Minor', 'Major', 'Critical']);
            $table->enum('status', ['Open', 'In Progress', 'Submitted', 'Resolved', 'Rejected'])->default('Open');
            $table->dateTime('deadline');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
