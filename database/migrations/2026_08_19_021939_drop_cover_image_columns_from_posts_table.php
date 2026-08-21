<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drop the legacy cover_image columns now that covers are stored
 * through the polymorphic images table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn(['cover_image', 'cover_image_thumb']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->string('cover_image')->nullable()->after('body');
            $table->string('cover_image_thumb')->nullable()->after('cover_image');
        });
    }
};
