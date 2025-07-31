<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResultSet extends Model
{
    protected $table = 'assessmentresultset';
    public $timestamps = false;

    // No primary key, no incrementing
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'AssessmentID',
        'QuestionID',
        'AnswerID',
        'DateCreate',
    ];
}
