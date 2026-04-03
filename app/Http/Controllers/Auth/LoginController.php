<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the user login form.
     */
    public function showLoginForm()
    {
        return view('auth.user-login');
    }

    /**
     * Show the admin login form.
     */
    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect to admin dashboard if logged in via admin login or based on user role/route
            // For now, if we have a destination intended, go there, else go home
            if ($request->is('admin/*') || $request->is('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Hoşgeldiniz, ' . Auth::user()->name);
        }

        throw ValidationException::withMessages([
            'email' => 'Hatalı e-posta adresi veya şifre.',
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('info', 'Oturum başarıyla kapatıldı.');
    }
}
