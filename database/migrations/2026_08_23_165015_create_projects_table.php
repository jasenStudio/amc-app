<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_seo')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('excerpt')->nullable();
            $table->string('client');
            $table->string('location')->nullable();
            $table->date('date');
            $table->string('status')->default('active');
            $table->boolean('featured')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('featured');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
