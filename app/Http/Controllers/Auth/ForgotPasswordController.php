<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class ForgotPasswordController extends Controller
{
    public function showLinkForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink()
    {
        // Implémenté via API
        return redirect()->route('password.request');
    }

    public function showResetForm()
    {
        return view('auth.reset-password');
    }

    public function reset()
    {
        // Implémenté via API
        return redirect()->route('password.reset');
    }
}
