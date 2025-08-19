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

    // Create participant
    $participant = Participant::create([
        'name' => $validated['name'],
        'phone_number' => $validated['phone_number'],
        'email' => $validated['email'],
        'event_code' => $eventCode,
    ]);

    // Clear all quiz-related session data for this event code
    session()->forget(["quiz_questions_$eventCode", "quiz_answers_$eventCode", "quiz_result_$eventCode", "quiz_completed_$eventCode"]);

    // Store participant info in session (this is key!)
    session([
        'participant_email' => $participant->email,
        'participant_id'    => $participant->id,
    ]);

    // Redirect to quiz page with new session parameter
    return redirect()->route('quiz.show', ['eventCode' => $eventCode])->with('new_session', true);
}
}
