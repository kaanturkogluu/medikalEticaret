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
            
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Kullanıcı önceden normal kayıt olduysa, sadece google id'sini güncelle
                $user->update([
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'avatar' => $user->avatar ?? $googleUser->avatar,
                ]);
            } else {
                // Yeni kayıt
                $user = User::create([
                    'email' => $googleUser->email,
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'avatar' => $googleUser->avatar,
                    'password' => bcrypt('google-user-' . \Illuminate\Support\Str::random(16)),
                    'email_verified_at' => now(),
                    'role' => 'user'
                ]);
            }

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
