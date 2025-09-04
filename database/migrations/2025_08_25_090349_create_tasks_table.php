<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create table without the team_id column
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('radio_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['todo', 'pending', 'done', 'late', 'expired'])->default('todo');
            $table->dateTime('deadline')->nullable();
            $table->json('task_docs')->nullable();
            $table->timestamps();
        });

        // Add team_id column after id
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('id')->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
