<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    // Table name (if not default pluralization)
    protected $table = 'assessment';

    // Primary key
    protected $primaryKey = 'AssessmentID';

    // Disable timestamps (since you use DateCreate/DateUpdate instead)
    public $timestamps = false;

    // Mass-assignable fields
    protected $fillable = [
        'ParticipantID',
        'TotalScore',
        'TotalQuestion',
        'AdminID',
        'DateCreate',
        'DateUpdate',
    ];

    // Optionally define relationships
public function participant()
{
    return $this->belongsTo(Participant::class, 'ParticipantID', 'id');
}

public function resultSets()
{
    return $this->hasMany(AssessmentResultSet::class, 'AssessmentID', 'AssessmentID');
}

}
