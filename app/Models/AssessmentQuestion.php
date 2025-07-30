<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    // Specify the table name (if it's not pluralized)
    protected $table = 'assessmentquestion';

    // Primary key
    protected $primaryKey = 'QuestionID';

    // Disable default timestamps (created_at / updated_at)
    public $timestamps = false;

    // Fillable fields for mass assignment
    protected $fillable = [
        'QuestionText',
        'QuestionImage',
        'DefaultTopic',
        'AdminID',
        'DateCreate',
        'DateUpdate',
    ];
}
