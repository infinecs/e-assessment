<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory; // You imported this, so use it

    protected $table = 'participants'; // explicitly define table name

    protected $fillable = [
        'name',
        'phone_number',
        'email',
    ];
}
