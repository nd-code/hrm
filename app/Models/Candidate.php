<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Candidate extends Model
{
    use HasFactory, LogsActivity;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'salary',
        'work_experience',
        'designation',
        'interview_date',
        'comment',
        'cv'
    ];
}
