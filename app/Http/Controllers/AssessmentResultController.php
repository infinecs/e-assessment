<?php

namespace App\Http\Controllers;

use App\Models\Assessment;

class AssessmentResultController extends Controller
{
    public function index()
    {
        // Fetch paginated results (10 per page)
        $records = Assessment::paginate(10);

        return view('assessment.results', compact('records'));
    }
}