<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class CLogin extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Login', [
            'title' => __('Login'),
            'description' => __('Please Login to your account'),
        ]);
    }

    public function auth(LoginRequest $request)
    {

        $request->validated();
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->intended(route('dashboard'))
                ->with('success', __('Login successful'));
        }
        return redirect()->back()
            ->withErrors(['email' => __('auth.failed')])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', __('You have been logged out successfully'));
    }
}
