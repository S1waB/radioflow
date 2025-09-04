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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->date('aired_on')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('description')->nullable();
            $table->json('episode_docs')->nullable();
            $table->string('conducteur_path')->nullable(); // file PDF/doc
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
