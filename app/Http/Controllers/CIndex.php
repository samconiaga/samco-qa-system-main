<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CIndex extends Controller
{
    public function index()
    {
        return inertia('Index', [
            'title' => __('Dashboard'),
            'description' => __('Welcome to the dashboard'),
        ]);
    }

    
}
