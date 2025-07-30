<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResultSet extends Model
{
    protected $table = 'assessmentresultset';
    public $timestamps = false;

    protected $primaryKey = null; // Composite key, handle manually
    public $incrementing = false;

    protected $fillable = [
        'AssessmentID',
        'QuestionID',
        'AnswerID',
        'DateCreate'
    ];
}
