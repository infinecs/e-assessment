<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory; // You imported this, so use it

    protected $table = 'participants';  // if not default
    protected $primaryKey = 'id';       // confirm that the column is literally `id`
    public $timestamps = true;   // explicitly define table name

    protected $fillable = [
        'name',
        'phone_number',
        'email',
    ];
}
