<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url')->nullable();
            $table->text('note')->nullable();
            $table->string('file')->nullable(); // store file path
            $table->enum('status', ['accepted', 'rejected', 'pending'])->default('pending');
            $table->foreignId('suggester_id')->constrained('users')->onDelete('cascade'); // user who suggested
            $table->foreignId('radio_id')->constrained('radios')->onDelete('cascade'); // related radio
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
