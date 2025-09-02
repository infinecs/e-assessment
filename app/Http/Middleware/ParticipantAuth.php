<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParticipantAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if participant is logged in via session
        $email = session('participant_email');
        
        if (!$email) {
            // Extract eventCode from the URL to redirect properly
            $eventCode = $request->route('eventCode');
            
            if ($eventCode) {
                return redirect()->route('participantRegister.show', $eventCode)
                    ->with('error', 'Please log in to access the quiz.');
            }
            
            // Fallback if no eventCode found
            return redirect('/login')
                ->with('error', 'Please log in to access this resource.');
        }
        
        return $next($request);
    }
}