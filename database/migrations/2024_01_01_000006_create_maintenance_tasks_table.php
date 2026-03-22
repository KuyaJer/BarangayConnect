<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reported_by');
            $table->uuid('assigned_to')->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('category', 100);
            $table->string('location');
            $table->string('priority', 50)->default('Normal');
            $table->string('status', 50)->default('Reported');
            $table->timestamps();

            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_tasks');
    }
};
