<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function index()
    {
        // Use Eloquent and paginate
        $records = AssessmentEvent::paginate(10); // 10 per page
        return view('assessment.events', compact('records'));
    }
}
