<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => 'admin123',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Test User',
                'password' => 'user123',
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
        $this->call([
            SettingSeeder::class,
            ChannelSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            CategoryAttributeSeeder::class,
            PageSeeder::class,
        ]);
    }
}
