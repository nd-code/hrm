<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Reminder extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'employee_id',
        'date',
        'subject',
        'description',
        'status',
    ];
}
