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

    // Handle AJAX requests
    if ($request->ajax()) {
        $html = '';
        foreach ($records as $row) {
            $html .= '<tr data-id="' . $row->AssessmentID . '"
                         data-participant-name="' . htmlspecialchars($row->participant->name ?? '') . '"
                         data-participant-phone="' . htmlspecialchars($row->participant->phone_number ?? '') . '"
                         data-participant-email="' . htmlspecialchars($row->participant->email ?? '') . '"
                         data-event-id="' . ($row->EventID ?? '') . '"
                         data-date-answered="' . $row->DateCreate . '"
                         class="bg-white border-b hover:bg-gray-50/50 dark:bg-zinc-700 dark:border-zinc-600">';

            $html .= '<td class="w-4 p-3">
                        <div class="flex items-center justify-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 border-gray-300 rounded bg-white">
                        </div>
                      </td>';

            $html .= '<td class="px-3 py-2">' . htmlspecialchars($row->participant->name ?? '-') . '</td>';
            $html .= '<td class="px-3 py-2">' . htmlspecialchars($row->participant->phone_number ?? '-') . '</td>';
            $html .= '<td class="px-3 py-2">' . htmlspecialchars($row->participant->email ?? '-') . '</td>';
            $html .= '<td class="px-3 py-2">' . htmlspecialchars($row->event->EventName ?? '-') . '</td>';
            $html .= '<td class="px-3 py-2">' . $row->TotalScore . ' / ' . $row->TotalQuestion . '</td>';
            // Percentage column
            if ($row->TotalQuestion > 0) {
                $percentage = number_format(($row->TotalScore / $row->TotalQuestion) * 100, 2) . '%';
            } else {
                $percentage = '-';
            }
            $html .= '<td class="px-3 py-2">' . $percentage . '</td>';
            $html .= '<td class="px-3 py-2">' . \Carbon\Carbon::parse($row->DateCreate)->format('d M Y') . '</td>';
            $html .= '<td class="px-3 py-2">
                        <button type="button" class="view-details px-4 py-1 text-sm bg-blue-500 text-blue-600 rounded hover:underline" data-id="' . $row->AssessmentID . '">
                            View
                        </button>
                      </td>';

            $html .= '</tr>';
        }

        // If no records found
        if ($records->isEmpty()) {
            $html = '<tr><td colspan="9" class="px-3 py-2 text-center">No records found</td></tr>';
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
                'from' => $records->firstItem(),
                'to' => $records->lastItem(),
                'links' => $records->render('pagination::tailwind')
            ]
        ]);
    }

    return view('assessment.results', compact('records', 'allEvents', 'allCategories', 'allTopics'));
}

    public function bulkDelete(Request $request)
    {
        // Accept both JSON and form data for ids
        $ids = $request->input('ids');
        if (empty($ids) && $request->isJson()) {
            $data = $request->json()->all();
            $ids = $data['ids'] ?? [];
        }
        if (!is_array($ids)) {
            // Try to decode if it's a string
            $ids = json_decode($ids, true);
        }
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No IDs provided', 'debug' => ['ids' => $ids, 'raw' => $request->all()]], 400);
        }
        // Find participant IDs for the selected assessments
        $participantIds = Assessment::whereIn('AssessmentID', $ids)->pluck('ParticipantID')->unique()->toArray();

        // First delete related resultset rows
        AssessmentResultSet::whereIn('AssessmentID', $ids)->delete();
        // Then delete the assessments themselves
        Assessment::whereIn('AssessmentID', $ids)->delete();

        // Delete participants only if they have no other assessments
        $deletedParticipants = [];
        foreach ($participantIds as $pid) {
            $remainingAssessments = Assessment::where('ParticipantID', $pid)->count();
            if ($remainingAssessments === 0) {
                \App\Models\Participant::where('id', $pid)->delete();
                $deletedParticipants[] = $pid;
            }
        }
        return response()->json(['status' => 'success', 'deleted_ids' => $ids, 'deleted_participants' => $deletedParticipants]);
    }

   public function details($id)
{
    $assessment = Assessment::with(['resultSets.question.answers'])->find($id);

    if (!$assessment) {
        return response()->json(['status' => 'error', 'message' => 'Assessment not found', 'debug' => ['id' => $id]], 404);
    }

    if ($assessment->resultSets->isEmpty()) {
        return response()->json([
            'status' => 'success',
            'questions' => []
        ]);
    }

    $questions = [];
    foreach ($assessment->resultSets as $resultSet) {
        $question = $resultSet->question;
        if (!$question) {
            $questions[] = [
                'text' => 'Question not found',
                'answers' => []
            ];
            continue;
        }
        $participantAnswerId = $resultSet->AnswerID;
        $answers = [];
        foreach ($question->answers as $answer) {
            $answers[] = [
                'text' => $answer->AnswerText,
                'is_participant' => ($answer->AnswerID == $participantAnswerId),
                'is_correct' => ($answer->ExpectedAnswer === 'Y')
            ];
        }
        $questions[] = [
            'text' => $question->QuestionText,
            'answers' => $answers
        ];
    }

    return response()->json([
        'status' => 'success',
        'questions' => $questions,
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
        'Event Code',
        'Score (Total Score / Total Questions)',
        'Percentage Score',
        'Date Answered'
    ];

    // CSV Data rows
    $rowNumber = 1;
    foreach ($records as $record) {
        $participant = $record->participant;
        $eventName = $record->event ? $record->event->EventName : 'N/A';
        $eventCode = $record->event && isset($record->event->EventCode) ? $record->event->EventCode : 'N/A';
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
            $eventCode,
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