<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE users SET role = 'super_admin' WHERE email = 'admin@example.com' AND role = 'admin'");
        DB::statement("UPDATE users SET role = 'pending' WHERE role IS NULL");

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->nullable()->default(null)->change();
        });

        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");
    }
};
