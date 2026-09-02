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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->dateTime('event_date');
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->string('location_url')->nullable();
            $table->string('speaker_name');
            $table->string('speaker_title')->nullable();
            $table->enum('status', ['upcoming', 'completed'])->default('upcoming');
            $table->string('recording_url')->nullable();
            $table->string('materials_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
