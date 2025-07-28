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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio'  => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', File::image()->max(2048)],
        ]);

        $user->name = $validated['name'];
        $user->bio = $validated['bio'] ?? $user->bio;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('settings.index')->with('success', 'Profile updated.');
    }

   public function updateAccount(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
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
        'password.confirmed' => 'Password confirmation does not match.',
        'password.min' => 'Password must be at least 8 characters.',
    ]);

    // Only update password
    $user->password = Hash::make($validated['password']);
    $user->save();

    return redirect()
        ->route('settings') // <- make sure route name exists
        ->with('success', 'Password updated successfully.');
}
}
