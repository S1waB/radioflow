<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('emission_task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('emission_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique('task_id'); // ensures each task links to only one emission
        });
    }

    public function down()
    {
        Schema::dropIfExists('emission_task');
    }
};
