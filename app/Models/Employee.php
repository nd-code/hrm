<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivity;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;
    
    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'phone',
        'position',
        'password',
        'pan_number',
        'address',
        'joining_date',
        'bank_details',
        'salary',
        'tds',
        'pt'
    ];
    protected $hidden = ['password'];
}
