<?php

namespace App\Http\Controllers;
use App\Models\AssessmentTopic;
use Illuminate\Http\Request;

class AssessmentTopicController extends Controller
{
    public function index()
{
    // Use the MODEL, not the controller
    $records = AssessmentTopic::paginate(10); // 10 per page
    return view('assessment.topic', compact('records'));
}
}
