<?php

namespace App\Http\Controllers;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
public function index()
{
    // Use the MODEL, not the controller
    $records = AssessmentCategory::paginate(10); // 10 per page
    return view('assessment.category', compact('records'));
}

}