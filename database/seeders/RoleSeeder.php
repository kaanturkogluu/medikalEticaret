<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set specific users as admins
        $admins = ['kaantrrkoglu@gmail.com', 'admin@example.com'];

        foreach ($admins as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update(['role' => 'admin']);
            }
        }

        // Set others as users (though default is user)
        User::whereNotIn('email', $admins)->update(['role' => 'user']);
    }
}
