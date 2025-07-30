<?php

namespace App\Http\Controllers;
use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;

class AssessmentQuestionController extends Controller
{
    public function index()
{
    // Use the MODEL, not the controller
    $records = AssessmentQuestion::paginate(10); // 10 per page
    return view('assessment.question', compact('records'));
}
}
