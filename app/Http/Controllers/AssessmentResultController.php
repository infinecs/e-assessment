<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentResultSet;

class AssessmentResultController extends Controller
{
  public function index()
{
    $records = Assessment::with('participant')
        ->orderBy('DateCreate', 'desc')
        ->orderBy('DateUpdate', 'desc') // fallback for time
        ->orderBy('AssessmentID', 'desc') // ensure stable order
        ->paginate(10);

    return view('assessment.results', compact('records'));
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


}