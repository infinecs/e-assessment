<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResultSet extends Model
{
    protected $table = 'assessmentresultset';
    public $timestamps = false;

    // No auto-incrementing primary key
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'AssessmentID',
        'QuestionID',
        'AnswerID',
        'DateCreate',
    ];

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'QuestionID', 'QuestionID');
    }

    public function answer()
    {
        return $this->belongsTo(AssessmentAnswer::class, 'AnswerID', 'AnswerID');
    }
}
