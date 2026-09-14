<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantsController extends Controller
{
    public function showRegisterForm($eventCode)
    {
        return view('participants.participantRegister', compact('eventCode'));
    }

    public function register(Request $request, $eventCode)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|confirmed',
            'password' => 'required|string',
        ]);

        $event = \App\Models\AssessmentEvent::where('EventCode', $eventCode)->first();
        if (!$event) {
            return back()->withErrors(['password' => 'Invalid assessment code.'])->withInput();
        }
        if ($validated['password'] !== $event->EventPassword) {
            return back()->withErrors(['password' => 'Incorrect assessment password.'])->withInput();
        }

        // Always create a fresh participant record for every registration/retake
        $participant = Participant::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'event_code' => $eventCode,
        ]);

        session()->forget(["quiz_questions_$eventCode", "quiz_answers_$eventCode", "quiz_result_$eventCode", "quiz_completed_$eventCode"]);

        session([
            'participant_email' => $participant->email,
            'participant_id'    => $participant->id,
        ]);

        return redirect()->route('quiz.show', ['eventCode' => $eventCode]);
    }
}
