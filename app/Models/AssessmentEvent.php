<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentEvent extends Model
{
    use HasFactory;

    // If table name is not plural (assessmentevent), specify it:
    protected $table = 'assessmentevent';

    // Primary key
    protected $primaryKey = 'EventID';

    // Laravel timestamps not used (because you have DateCreate/DateUpdate)
    public $timestamps = false;

    // Fillable columns
    protected $fillable = [
        'CategoryID',
        'EventName',
        'EventCode',
        'QuestionLimit',
        'DurationEachQuestion',
        'TopicID',
        'StartDate',
        'EndDate',
        'AdminID',
        'DateCreate',
        'DateUpdate',
    ];

    
}
