<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('library_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('slug');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->json('summary_points')->nullable();
            $table->longText('body');
            $table->text('research_box')->nullable();
            $table->text('action_title')->nullable();
            $table->json('action_checklist')->nullable();
            $table->unsignedSmallInteger('reading_minutes')->default(1);
            $table->timestamps();

            $table->unique(['library_item_id', 'slug']);
            $table->unique(['library_item_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('library_chapters')->exists()) {
            throw new RuntimeException('Chapter data must be preserved before rolling back the ebook chapter schema.');
        }

        Schema::dropIfExists('library_chapters');
    }
};
