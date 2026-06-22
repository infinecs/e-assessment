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
        \Log::info('Registration attempt', [
            'eventCode' => $eventCode,
            'form_data' => $request->except('password', 'email_confirmation')
        ]);

        // Validate basic inputs (email uniqueness is handled separately to allow re-entry)
        $validated = $request->validate(
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|confirmed',
                'password' => 'required|string',
            ],
            [
                'email.confirmed' => 'Email addresses do not match. Please confirm your email correctly.',
                'email.email' => 'Please enter a valid email address.',
                'name.required' => 'Full name is required.',
                'password.required' => 'Password (Assessment Code) is required.',
            ]
        );

        \Log::info('Validation passed', ['email' => $validated['email']]);

        // Fetch the event and verify the assessment password
        $event = \App\Models\AssessmentEvent::where('EventCode', $eventCode)->first();
        if (!$event) {
            \Log::warning('Event not found', ['eventCode' => $eventCode]);
            return back()->withErrors(['eventCode' => 'Invalid assessment code.'])->withInput();
        }

        \Log::info('Event found', ['eventCode' => $eventCode, 'eventPassword' => $event->EventPassword]);

        // Trim the validated password to handle any whitespace
        $providedPassword = trim($validated['password']);
        $expectedPassword = trim($event->EventPassword);

        if ($providedPassword !== $expectedPassword) {
            \Log::warning('Incorrect password', [
                'provided' => $providedPassword,
                'expected' => $expectedPassword,
                'provided_length' => strlen($providedPassword),
                'expected_length' => strlen($expectedPassword)
            ]);
            return back()->withErrors(['password' => 'Incorrect assessment password. Please check and try again.'])->withInput();
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
