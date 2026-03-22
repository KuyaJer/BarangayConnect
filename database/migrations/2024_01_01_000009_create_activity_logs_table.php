<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('action', 50);            // login, logout, created, updated, deleted, status_changed, password_changed
            $table->string('subject_type', 100)->nullable(); // ServiceRequest, Complaint, etc.
            $table->string('subject_id', 36)->nullable();
            $table->text('description');
            $table->json('properties')->nullable();  // extra context (old/new values)
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('subject_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
