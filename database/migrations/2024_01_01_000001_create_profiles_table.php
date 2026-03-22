<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('surname', 100)->nullable();
            $table->string('suffix', 20)->nullable();
            $table->mediumText('avatar_url')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('current_place')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('civil_status', 50)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
