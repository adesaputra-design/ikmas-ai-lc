<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('library_items', function (Blueprint $table) {
            $table->enum('type', ['book', 'podcast', 'academic', 'ebook'])->change();
            $table->string('series_name')->nullable()->after('reading_time');
            $table->unsignedInteger('edition_number')->nullable()->after('series_name');
            $table->boolean('is_public')->default(false)->after('is_featured');
        });

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

    public function down(): void
    {
        // Dicek sebelum menghapus apa pun agar rollback tidak pernah membuang naskah ebook.
        if (DB::table('library_items')->where('type', 'ebook')->exists()) {
            throw new RuntimeException('Rollback dibatalkan: masih ada item bertipe ebook di library_items. Hapus atau pindahkan data ebook terlebih dahulu.');
        }

        Schema::dropIfExists('library_chapters');

        Schema::table('library_items', function (Blueprint $table) {
            $table->dropColumn(['series_name', 'edition_number', 'is_public']);
        });

        Schema::table('library_items', function (Blueprint $table) {
            $table->enum('type', ['book', 'podcast', 'academic'])->change();
        });
    }
};
