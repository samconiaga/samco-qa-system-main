<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CResetPassword extends Controller
{
    public function index()
    {
       return inertia('Auth/Password/ResetPassword', [
            'title' => __('Reset Password'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    }
    
}
