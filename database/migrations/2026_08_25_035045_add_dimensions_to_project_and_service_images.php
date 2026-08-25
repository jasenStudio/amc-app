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
        Schema::table('project_images', function (Blueprint $table) {
            $table->unsignedInteger('width')->nullable()->after('image_path');
            $table->unsignedInteger('height')->nullable()->after('width');
        });

        Schema::table('service_images', function (Blueprint $table) {
            $table->unsignedInteger('width')->nullable()->after('image_path');
            $table->unsignedInteger('height')->nullable()->after('width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn(['width', 'height']);
        });

        Schema::table('service_images', function (Blueprint $table) {
            $table->dropColumn(['width', 'height']);
        });
    }
};
