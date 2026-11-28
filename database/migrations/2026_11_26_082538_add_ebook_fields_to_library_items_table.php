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
        Schema::table('library_items', function (Blueprint $table) {
            $table->string('type', 20)->change();
            $table->string('series_name')->nullable();
            $table->unsignedInteger('edition_number')->nullable();
            $table->boolean('is_public')->default(false);
            $table->json('assessment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('library_items')->where('type', 'ebook')->exists()) {
            throw new RuntimeException('Ebook data must be preserved before rolling back the ebook schema.');
        }

        Schema::table('library_items', function (Blueprint $table) {
            $table->dropColumn(['series_name', 'edition_number', 'is_public', 'assessment']);
            $table->enum('type', ['book', 'podcast', 'academic'])->change();
        });
    }
};
