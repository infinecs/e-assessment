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
        // Validate basic inputs (email uniqueness is handled separately to allow re-entry)
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|confirmed',
            'password' => 'required|string',
        ]);

        // Fetch the event and verify the assessment password
        $event = \App\Models\AssessmentEvent::where('EventCode', $eventCode)->first();
        if (!$event) {
            return back()->withErrors(['password' => 'Invalid assessment code.'])->withInput();
        }
        if ($validated['password'] !== $event->EventPassword) {
            return back()->withErrors(['password' => 'Incorrect assessment password.'])->withInput();
        }

        // If the participant already registered today, allow re-entry instead of blocking
        $today = now()->toDateString();
        $existingParticipant = Participant::where('email', $validated['email'])
            ->whereDate('created_at', $today)
            ->first();

        if ($existingParticipant) {
            session()->forget(["quiz_questions_$eventCode", "quiz_answers_$eventCode", "quiz_result_$eventCode", "quiz_completed_$eventCode"]);
            session([
                'participant_email' => $existingParticipant->email,
                'participant_id'    => $existingParticipant->id,
            ]);
            return redirect()->route('quiz.show', ['eventCode' => $eventCode]);
        }

        // Create new participant record
        $participant = Participant::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'event_code' => $eventCode,
        ]);

        // Clear any stale quiz session data for this event code
        session()->forget(["quiz_questions_$eventCode", "quiz_answers_$eventCode", "quiz_result_$eventCode", "quiz_completed_$eventCode"]);

        // Store participant info in session
        session([
            'participant_email' => $participant->email,
            'participant_id'    => $participant->id,
        ]);

        return redirect()->route('quiz.show', ['eventCode' => $eventCode])->with('new_session', true);
    }
}
