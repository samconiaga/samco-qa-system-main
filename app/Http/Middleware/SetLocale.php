<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Ambil bahasa dari session jika ada
        if ($request->session()->has('locale')) {
            App::setLocale($request->session()->get('locale', 'id')); // default 'id'
        } else {
            // 2. Kalau tidak ada, ambil dari header Accept-Language
            $lang = $request->header('Accept-Language', 'id'); // default 'id'
            $availableLanguages = ['en', 'id'];
            if (!in_array($lang, $availableLanguages)) {
                $lang = 'id';
            }
            App::setLocale($lang);
        }

        return $next($request);
    }
}
