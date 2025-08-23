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
        Schema::create('radios', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path', 2048)->nullable();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('Country')->nullable();           // Add status column with ENUM or string, default to 'active'
            $table->enum('status', ['active', 'desactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radios');
    }
};
