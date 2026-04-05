<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isUser()) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->isAdmin()) {
             return redirect('/admin')->with('info', 'Panel girişi yapıldı, kullanıcı alanına geçmek için çıkış yapıp kullanıcı hesabı ile giriniz.');
        }

        return redirect('/login')->with('error', 'Bu sayfaya erişim için giriş yapmanız gerekmektedir.');
    }
}
