<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            // Email not found
            return back()->withErrors([
                'email' => 'This email is not registered in our system.',
            ])->withInput();
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Password mismatch
            return back()->withErrors([
                'password' => 'Incorrect password.',
            ])->withInput();
        }

        // Successful login
        $request->session()->regenerate();
        $role = Auth::user()->roles;

        return match ($role) {
            'admin' => redirect()->intended('/admin-dashboard'),
            'user' => redirect()->intended('/user-dashboard'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
