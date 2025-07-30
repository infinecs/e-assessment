<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentTopic extends Model
{
    use HasFactory;

    // Explicit table name since Laravel expects "assessment_topics"
    protected $table = 'assessmenttopic';

    // Primary key
    protected $primaryKey = 'TopicID';

    // Disable default timestamps since your table uses DateCreate / DateUpdate instead
    public $timestamps = false;

    // Fields that can be mass assigned
    protected $fillable = [
        'TopicName',
        'QuestionID',
        'AdminID',
        'DateCreate',
        'DateUpdate',
    ];
}


