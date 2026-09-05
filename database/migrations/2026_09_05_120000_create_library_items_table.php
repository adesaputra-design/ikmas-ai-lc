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
        Schema::create('library_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['book', 'podcast', 'academic']);
            $table->string('category', 60)->default('Umum');
            $table->text('summary_preview');
            $table->longText('content')->nullable();

            // Metadata Buku
            $table->string('author_name')->nullable();
            $table->string('reading_time')->nullable();

            // Metadata Podcast
            $table->string('podcast_source')->nullable();
            $table->text('media_embed_url')->nullable();
            $table->string('duration')->nullable();

            // Metadata Karya Ilmiah (Academic)
            $table->enum('academic_degree', ['skripsi', 'tesis', 'disertasi', 'jurnal'])->nullable();
            $table->string('institution')->nullable();
            $table->integer('publication_year')->nullable();
            $table->string('co_authors')->nullable();
            $table->string('external_url')->nullable();
            $table->string('file_path')->nullable();

            // Kontrol & Kurasi
            $table->string('cover_image')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->text('rejection_note')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_items');
    }
};
