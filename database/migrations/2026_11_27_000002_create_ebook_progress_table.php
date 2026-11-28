<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('library_item_id')->constrained()->cascadeOnDelete();
            $table->json('checklist_state')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'library_item_id']);
        });
    }

    public function down(): void
    {
        if (DB::table('ebook_progress')->exists()) {
            throw new RuntimeException('Ebook progress must be preserved before rolling back its schema.');
        }

        Schema::dropIfExists('ebook_progress');
    }
};
