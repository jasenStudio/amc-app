<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->superAdmin()->create([
            'name' => 'Admin User',
            'email' => 'jsalgadoecheverria@gmail.com',
            'password' => env('ADMIN_SEED_PASSWORD'),
        ]);

        User::factory()->editor()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
        ]);

        $this->call(ServiceSeeder::class);
    }
}
