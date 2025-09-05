<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emission_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path'); // store path to uploaded doc
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('materials');
    }
};
