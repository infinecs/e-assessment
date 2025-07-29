<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantsController extends Controller
{
    public function store(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|regex:/^[0-9]+$/|max:20',
            'email' => 'required|email|unique:participants,email',
        ]);

        // Save participant
        Participant::create($validated);

        // Redirect to quiz page
        return redirect('/quiz');

    }
}
