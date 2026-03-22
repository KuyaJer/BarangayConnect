<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->text('address');
            $table->string('contact_number', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('household_id', 50)->nullable();
            $table->string('purok', 100)->nullable();
            $table->integer('resident_id_number')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
