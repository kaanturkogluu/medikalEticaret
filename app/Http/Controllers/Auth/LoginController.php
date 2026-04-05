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
            $user = Auth::user();

            // Guard: If trying to log in at the admin portal but not an admin
            if ($request->is('admin/login')) {
                if (!$user->isAdmin()) {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Bu portal sadece yönetici girişi içindir.']);
                }
            }

            $request->session()->regenerate();

            // Smart redirection based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('user.dashboard'))
                ->with('success', 'Hoşgeldiniz, ' . $user->name . '!');
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
