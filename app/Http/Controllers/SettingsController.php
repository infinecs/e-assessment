<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        return view('assessment.settings');
    }


    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,}$/', // stricter format, lowercase only
                'unique:users,email,' . $user->id,
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'email.regex' => 'Email must be lowercase and a valid format.',
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        // Update email if changed
        $user->email = strtolower($validated['email']);
        // Update password
        $user->password = $validated['password'];
        $user->save();

        return redirect()
            ->route('settings')
            ->with('success', 'Account updated successfully.');
    }
}
