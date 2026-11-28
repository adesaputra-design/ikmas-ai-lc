<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebook_progress', function (Blueprint $table): void {
            $table->foreignId('last_chapter_id')->nullable()->constrained('library_chapters')->nullOnDelete();
            $table->json('assessment_results')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ebook_progress', function (Blueprint $table): void {
            $table->dropForeign(['last_chapter_id']);
            $table->dropColumn(['last_chapter_id', 'assessment_results']);
        });
    }
};
