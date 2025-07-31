<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantsController extends Controller
{
    public function showRegisterForm($eventCode)
    {
        // Pass eventCode to the view
        return view('participants.participantRegister', compact('eventCode'));
    }

    public function register(Request $request, $eventCode)
    {
        // Validate inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|regex:/^[0-9]+$/|max:20',
            'email' => 'required|email|unique:participants,email',
        ]);

        // Save participant and link to event code
        Participant::create([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'event_code' => $eventCode, // make sure this column exists
        ]);

        // Redirect to quiz page for that event
        return redirect()->route('quiz.show', ['eventCode' => $eventCode]);
    }
}
