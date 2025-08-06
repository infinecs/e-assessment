<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment;
use App\Models\AssessmentResultSet;

class AssessmentResultController extends Controller
{
  public function index(Request $request)
{
    // Start building the query
    $query = Assessment::with(['participant', 'event'])
        ->orderBy('DateCreate', 'desc')
        ->orderBy('DateUpdate', 'desc') // fallback for time
        ->orderBy('AssessmentID', 'desc'); // ensure stable order

    // Apply server-side search filter
    $search = $request->input('search');
    if ($search) {
        $query->whereHas('participant', function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('phone_number', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    // Apply event filter
    $events = $request->input('events');
    if ($events) {
        $eventIds = explode(',', $events);
        $query->whereIn('EventID', $eventIds);
    }

    // Apply category filter (through events)
    $categories = $request->input('categories');
    if ($categories) {
        $categoryIds = explode(',', $categories);
        $query->whereHas('event', function ($q) use ($categoryIds) {
            $q->whereIn('CategoryID', $categoryIds);
        });
    }

    // Apply topic filter (through event topics)
    $topics = $request->input('topics');
    if ($topics) {
        $topicIds = explode(',', $topics);
        $query->whereHas('event', function ($q) use ($topicIds) {
            $q->where(function ($subQuery) use ($topicIds) {
                foreach ($topicIds as $topicId) {
                    $subQuery->orWhere('TopicID', 'LIKE', "%{$topicId}%");
                }
            });
        });
    }

    // Apply date filter
    $dateAnswered = $request->input('date_answered');
    if ($dateAnswered) {
        $query->whereDate('DateCreate', $dateAnswered);
    }

    // Get paginated results
    $records = $query->paginate(10);

    // Append query parameters to pagination links
    $records->appends($request->query());

    // Get all events for filter dropdown
    $allEvents = DB::table('assessmentevent')
        ->select('EventID', 'EventName')
        ->orderBy('EventName')
        ->get();
        
    // Get all categories for filter dropdown
    $allCategories = DB::table('assessmentcategory')
        ->select('CategoryID', 'CategoryName')
        ->orderBy('CategoryName')
        ->get();
        
    // Get all topics for filter dropdown
    $allTopics = DB::table('assessmenttopic')
        ->select('TopicID', 'TopicName')
        ->orderBy('TopicName')
        ->get();

    return view('assessment.results', compact('records', 'allEvents', 'allCategories', 'allTopics'));
}

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            // First delete related resultset rows
            AssessmentResultSet::whereIn('AssessmentID', $ids)->delete();

            // Then delete the assessments themselves
            Assessment::whereIn('AssessmentID', $ids)->delete();

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'no_ids'], 400);
    }

   public function details($id)
{
    $assessment = Assessment::with(['resultSets.question.answers'])
        ->find($id);

    if (!$assessment) {
        return response()->json(['status' => 'error', 'message' => 'Assessment not found', 'debug' => ['id' => $id]], 404);
    }

    if ($assessment->resultSets->isEmpty()) {
        return response()->json([
            'status' => 'success',
            'results' => []
        ]);
    }

    $results = $assessment->resultSets->map(function ($result) {
        $question = $result->question;
        if (!$question) {
            return [
                'question' => 'Question not found',
                'participantAnswer' => null,
                'correctAnswer' => null,
            ];
        }
        $participantAnswer = $result->AnswerID
            ? optional($question->answers->firstWhere('AnswerID', $result->AnswerID))->AnswerText
            : null;

        $correctAnswer = optional($question->answers->firstWhere('ExpectedAnswer', 'Y'))->AnswerText;

        return [
            'question' => $question->QuestionText ?? 'Unknown',
            'participantAnswer' => $participantAnswer,
            'correctAnswer' => $correctAnswer,
        ];
    });

    return response()->json([
        'status' => 'success',
        'results' => $results,
    ]);
}

public function exportExcel(Request $request)
{
    // Get filter parameters
    $search = $request->input('search');
    $dateAnswered = $request->input('date_answered');
    $events = $request->input('events') ? explode(',', $request->input('events')) : [];
    $categories = $request->input('categories') ? explode(',', $request->input('categories')) : [];
    $topics = $request->input('topics') ? explode(',', $request->input('topics')) : [];

    // Build query with filters
    $query = Assessment::with(['participant', 'event', 'resultSets.question.answers'])
        ->orderBy('DateCreate', 'desc');

    // Apply search filter (name, phone, email)
    if ($search) {
        $query->whereHas('participant', function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('phone_number', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    // Apply event filter
    if (!empty($events)) {
        $query->whereIn('EventID', $events);
    }

    // Apply category filter (through events)
    if (!empty($categories)) {
        $query->whereHas('event', function ($q) use ($categories) {
            $q->whereIn('CategoryID', $categories);
        });
    }

    // Apply topic filter (through event topics)
    if (!empty($topics)) {
        $query->whereHas('event', function ($q) use ($topics) {
            $q->where(function ($subQuery) use ($topics) {
                foreach ($topics as $topicId) {
                    $subQuery->orWhere('TopicID', 'LIKE', "%{$topicId}%");
                }
            });
        });
    }

    // Apply date filter
    if ($dateAnswered) {
        $query->whereDate('DateCreate', $dateAnswered);
    }

    // Get the filtered data
    $records = $query->get();

    // Create CSV content
    $csvData = [];
    
    // CSV Headers
    $csvData[] = [
        'No',
        'Assessment ID',
        'Participant Name',
        'Phone Number',
        'Email',
        'Event Name',
        'Score (Total Score / Total Questions)',
        'Percentage Score',
        'Date Answered'
    ];

    // CSV Data rows
    $rowNumber = 1;
    foreach ($records as $record) {
        $participant = $record->participant;
        $eventName = $record->event ? $record->event->EventName : 'N/A';
        $percentage = $record->TotalQuestion > 0 ? round(($record->TotalScore / $record->TotalQuestion) * 100, 2) : 0;
        
        // Format score as text to prevent Excel from interpreting as date
        $scoreText = "'" . $record->TotalScore . ' / ' . $record->TotalQuestion;
        
        $csvData[] = [
            $rowNumber,
            $record->AssessmentID,
            $participant->name ?? 'N/A',
            $participant->phone_number ?? 'N/A',
            $participant->email ?? 'N/A',
            $eventName,
            $scoreText,
            $percentage . '%',
            \Carbon\Carbon::parse($record->DateCreate)->format('Y-m-d H:i:s')
        ];
        
        $rowNumber++;
    }

    // Generate filename with timestamp
    $filename = 'assessment-results-' . date('Y-m-d-H-i-s') . '.csv';

    // Create response
    $response = response()->streamDownload(function () use ($csvData) {
        $handle = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);

    return $response;
}


}