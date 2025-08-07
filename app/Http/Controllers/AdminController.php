<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $passed = \App\Models\Assessment::whereRaw('TotalScore >= (TotalQuestion * 0.7)')->count();
        $failed = \App\Models\Assessment::whereRaw('TotalScore < (TotalQuestion * 0.7)')->count();

        // Get event participation counts
        $eventData = \App\Models\Assessment::select('EventID', \DB::raw('count(*) as count'))
            ->groupBy('EventID')
            ->get();

        // Get event names
        $eventNames = \App\Models\AssessmentEvent::whereIn('EventID', $eventData->pluck('EventID'))
            ->pluck('EventName', 'EventID');

        // Prepare chart data
        $eventChartLabels = [];
        $eventChartCounts = [];
        foreach ($eventData as $row) {
            $eventChartLabels[] = $eventNames[$row->EventID] ?? ('Event ' . $row->EventID);
            $eventChartCounts[] = $row->count;
        }

        return view('assessment.index', compact('passed', 'failed', 'eventChartLabels', 'eventChartCounts'));
    }
}