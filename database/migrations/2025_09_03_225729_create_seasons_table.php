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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emission_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number'); // starts at 1 and increments
            $table->timestamps();

            $table->unique(['emission_id', 'number']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('seasons');
    }
};