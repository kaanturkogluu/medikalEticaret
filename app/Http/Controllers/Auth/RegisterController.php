<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration form submission.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:8|confirmed',
            'website_url' => 'prohibited',
        ], [
            'name.required'      => 'Ad Soyad alanı zorunludur.',
            'email.required'     => 'E-posta alanı zorunludur.',
            'email.email'        => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique'       => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required'  => 'Şifre alanı zorunludur.',
            'password.min'       => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'                           => $request->name,
            'email'                          => $request->email,
            'password'                       => Hash::make($request->password),
            'email_verification_code'        => $code,
            'email_verification_expires_at'  => Carbon::now()->addMinutes(30),
        ]);

        // Send verification email
        Mail::send('emails.verify-email', ['user' => $user, 'code' => $code], function ($m) use ($user) {
            $m->to($user->email, $user->name)
              ->subject('E-posta Doğrulama Kodunuz');
        });

        return redirect()->route('verify.form', ['email' => $user->email])
            ->with('success', 'Kayıt başarılı! E-postanıza gönderilen doğrulama kodunu giriniz.');
    }

    /**
     * Show the email verification form.
     */
    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-email', ['email' => $request->query('email')]);
    }

    /**
     * Handle email verification code submission.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Kullanıcı bulunamadı.']);
        }

        if ($user->email_verified_at) {
            return redirect()->route('home')->with('info', 'E-posta adresiniz zaten doğrulanmış.');
        }

        if ($user->email_verification_expires_at < Carbon::now()) {
            return back()->withErrors(['code' => 'Doğrulama kodunun süresi dolmuş. Lütfen tekrar kod isteyin.']);
        }

        if ($user->email_verification_code !== $request->code) {
            return back()->withErrors(['code' => 'Geçersiz doğrulama kodu.']);
        }

        $user->update([
            'email_verified_at'              => Carbon::now(),
            'email_verification_code'        => null,
            'email_verification_expires_at'  => null,
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'E-postanız doğrulandı! Hoş geldiniz, ' . $user->name . '!');
    }

    /**
     * Resend the verification code.
     */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->whereNull('email_verified_at')->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Kullanıcı bulunamadı veya zaten doğrulanmış.']);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'email_verification_code'       => $code,
            'email_verification_expires_at' => Carbon::now()->addMinutes(30),
        ]);

        Mail::send('emails.verify-email', ['user' => $user, 'code' => $code], function ($m) use ($user) {
            $m->to($user->email, $user->name)
              ->subject('E-posta Doğrulama Kodunuz');
        });

        return back()->with('success', 'Yeni doğrulama kodu e-postanıza gönderildi.');
    }
}
