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
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('email');
            $table->string('alumni_year')->nullable()->after('whatsapp_number');
            $table->text('bio')->nullable()->after('alumni_year');
            $table->enum('role', ['admin', 'member'])->default('member')->after('bio');
            $table->string('avatar')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'alumni_year', 'bio', 'role', 'avatar']);
        });
    }
};
