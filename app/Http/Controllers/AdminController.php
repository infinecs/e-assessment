<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Load your admin dashboard view
        return view('assessment.index');
    }
}