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
      Schema::create('emissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('animateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('logo_path')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->json('emission_docs')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('emissions');
    }
};
