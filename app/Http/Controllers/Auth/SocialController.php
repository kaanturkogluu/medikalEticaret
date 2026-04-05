<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
                'avatar' => $googleUser->avatar,
                'password' => bcrypt('google-user-' . $googleUser->id), // Dummy password
                'email_verified_at' => now(), // Assume google accounts are verified
                'role' => 'user' // Default role
            ]);

            Auth::login($user);

            // Smart Redirect
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('user.dashboard');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Google ile giriş yaparken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
