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
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('level', ['beginner', 'explorer', 'practitioner'])->default('beginner');
            $table->enum('pillar', ['basics', 'tools', 'productivity', 'workflow', 'opportunity'])->default('basics');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->unsignedInteger('reading_minutes')->default(5);
            $table->string('video_url')->nullable();
            $table->string('slide_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};
