<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentEvent extends Model
{
    protected $table = 'assessmentevent';
    protected $primaryKey = 'EventID';
    public $timestamps = false;

    protected $fillable = [
        'EventName',
        'EventCode', 
        'CategoryID',
        'TopicID',
        'TopicWeightages',
        'QuestionLimit',
        'DurationEachQuestion',
        'StartDate',
        'EndDate',
        'DateCreate',
        'DateUpdate',
        'AdminID'
    ];

    protected $casts = [
        'TopicWeightages' => 'json', // This will handle JSON encoding/decoding
        'StartDate' => 'datetime',
        'EndDate' => 'datetime',
        'DateCreate' => 'datetime',
        'DateUpdate' => 'datetime',
        'QuestionLimit' => 'integer',
        'DurationEachQuestion' => 'integer',
        'CategoryID' => 'integer',
        'AdminID' => 'integer'
    ];

    // Accessor for TopicWeightages to handle legacy string format
    public function getTopicWeightagesAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // If it's already an array (from JSON cast), return it
        if (is_array($value)) {
            return $value;
        }
        
        // If it's a JSON string, decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : [];
        }
        
        return [];
    }

    // Mutator for TopicWeightages to ensure proper JSON storage
    public function setTopicWeightagesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['TopicWeightages'] = json_encode($value);
        } elseif (is_string($value)) {
            $this->attributes['TopicWeightages'] = $value;
        } else {
            $this->attributes['TopicWeightages'] = json_encode([]);
        }
    }
}