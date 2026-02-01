<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        if (! User::where('email', 'owner@example.com')->exists()) {
            User::create([
                'name' => 'Owner Demo',
                'email' => 'owner@example.com',
                'password' => Hash::make('owner'),
                'role' => 'owner',
                'is_active' => true,
            ]);
        }

        $this->call(ArticleSeeder::class);
    }
}
