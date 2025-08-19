<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $passed = \App\Models\Assessment::whereRaw('TotalScore >= (TotalQuestion * 0.7)')->count();
        $failed = \App\Models\Assessment::whereRaw('TotalScore < (TotalQuestion * 0.7)')->count();

        // Stat widgets
        $totalAssessments = \App\Models\Assessment::count();
        $totalParticipants = \App\Models\Participant::count();
        $passRate = $totalAssessments > 0 ? number_format(($passed / $totalAssessments) * 100, 2) : 0;
        $averageScore = \App\Models\Assessment::where('TotalQuestion', '>', 0)
            ->selectRaw('AVG(TotalScore / TotalQuestion * 100) as avg_percentage')
            ->value('avg_percentage');
        $averageScore = $averageScore ? number_format($averageScore, 2) : 0;

        // Top performers (top 5 by percentage)
        $topPerformers = \App\Models\Assessment::with(['participant', 'event'])
            ->where('TotalQuestion', '>', 0)
            ->get()
            ->map(function($row) {
                $row->percentage = $row->TotalQuestion > 0 ? ($row->TotalScore / $row->TotalQuestion) * 100 : 0;
                $row->name = $row->participant->name ?? '-';
                $row->email = $row->participant->email ?? '-';
                $row->assessment_name = $row->event->EventName ?? '-';
                return $row;
            })
            ->sortByDesc('percentage')
            ->take(5);

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

        return view('assessment.index', compact(
            'passed', 'failed', 'eventChartLabels', 'eventChartCounts',
            'totalAssessments', 'totalParticipants', 'passRate', 'averageScore', 'topPerformers'
        ));
    }
}