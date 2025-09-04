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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('radio_id'); // new column
            $table->string('name'); // unique per radio only
            $table->text('description')->nullable();
            $table->unsignedInteger('hierarchy_level')->default(7); // 1 is highest (admin)
            $table->timestamps();

            $table->foreign('radio_id')->references('id')->on('radios')->onDelete('cascade');
            $table->unique(['radio_id', 'name']); // unique per radio
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
