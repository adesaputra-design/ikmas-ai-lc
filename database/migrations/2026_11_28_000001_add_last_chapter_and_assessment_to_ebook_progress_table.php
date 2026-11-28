<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (DB::table('ebook_progress')->whereNotNull('last_chapter_id')->orWhereNotNull('assessment_results')->exists()) {
            throw new RuntimeException('Reading positions and assessment history must be preserved before rolling back this migration.');
        }

        Schema::table('ebook_progress', function (Blueprint $table): void {
            $table->dropForeign(['last_chapter_id']);
            $table->dropColumn(['last_chapter_id', 'assessment_results']);
        });
    }
};
