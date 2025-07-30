<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    use HasFactory;

    // Specify the table name (Laravel would otherwise look for "assessment_categories")
    protected $table = 'assessmentcategory';

    // Primary key column
    protected $primaryKey = 'CategoryID';

    // If your table does not have created_at and updated_at
    public $timestamps = false;

    // Mass assignable columns
    protected $fillable = [
        'CategoryName',
        'AdminID',
        'DateCreate',
        'DateUpdate'
    ];
}

