<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the request for a password reset link.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.exists' => 'Bu e-posta adresine sahip bir kullanıcı bulunamadı.',
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        $user = User::where('email', $request->email)->first();

        // Send reset email
        Mail::send('emails.password-reset', ['user' => $user, 'token' => $token], function ($m) use ($user) {
            $m->to($user->email, $user->name)
              ->subject('Şifre Sıfırlama Talebi');
        });

        return back()->with('success', 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Handle the password reset submission.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'E-posta alanı zorunludur.',
            'email.exists' => 'Kullanıcı bulunamadı.',
            'password.required' => 'Yeni şifre alanı zorunludur.',
            'password.min' => 'Yeni şifre en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Geçersiz veya süresi dolmuş şifre sıfırlama bağlantısı.']);
        }

        // Check expiry (e.g., 60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Şifre sıfırlama bağlantısının süresi dolmuş.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Şifreniz başarıyla sıfırlandı. Yeni şifrenizle giriş yapabilirsiniz.');
    }
}
