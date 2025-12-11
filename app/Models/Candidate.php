<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'salary',
        'work_experience',
        'designation',
        'interview_date',
        'feedback',
        'cv'
    ];
}
