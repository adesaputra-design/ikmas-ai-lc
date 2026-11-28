<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('library_chapters', function (Blueprint $table) {
            $table->json('approved_quotes')->nullable();
        });
    }

    public function down(): void
    {
        if (DB::table('library_chapters')->whereNotNull('approved_quotes')->exists()) {
            throw new RuntimeException('Approved quotation data must be preserved before rolling back this migration.');
        }

        Schema::table('library_chapters', function (Blueprint $table) {
            $table->dropColumn('approved_quotes');
        });
    }
};
