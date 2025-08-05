<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAnswer extends Model
{
    use HasFactory;

    protected $table = 'assessmentanswer'; // set table name explicitly if it doesn't follow Laravel naming

    protected $primaryKey = 'AnswerID';
    public $timestamps = false; // because your table uses DateCreate / DateUpdate, not Laravel timestamps

    protected $fillable = [
        'QuestionID',
        'AnswerType',
        'AnswerText',
        'ExpectedAnswer',
        'AdminID',
        'DateCreate',
        'DateUpdate',
    ];

    // Default values for attributes
    protected $attributes = [
        'AdminID' => 0,
        'AnswerType' => 'T',
    ];

    // Relationship to Question
    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'QuestionID', 'QuestionID');
    }
}
