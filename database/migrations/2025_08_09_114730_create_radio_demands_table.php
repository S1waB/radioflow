<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radio_demands', function (Blueprint $table) {
            $table->id();
            $table->string('radio_name');
            $table->text('description');
            $table->date('founding_date');
            $table->string('manager_name');
            $table->string('manager_email');
            $table->string('manager_phone');
            $table->string('logo_path')->nullable();
            $table->json('team_members'); // Stores array of team members
            $table->string('status')->default('pending'); // pending, in_process, approved, rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radio_demands');
    }
};
