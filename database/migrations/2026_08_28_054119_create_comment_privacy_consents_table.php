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
        Schema::create('comment_privacy_consents', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable');
            $table->string('commenter_name')->nullable();
            $table->string('commenter_email')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('accepted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_privacy_consents');
    }
};
