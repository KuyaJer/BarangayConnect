<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catalog of all available permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->unique();          // e.g. service_requests.create
            $table->string('display_name', 150);            // e.g. Submit Service Requests
            $table->string('group', 60);                    // e.g. Service Requests
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Which permissions each role has
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->string('role', 30);
            $table->string('permission', 100);
            $table->primary(['role', 'permission']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
