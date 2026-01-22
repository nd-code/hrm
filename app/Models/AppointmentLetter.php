<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class AppointmentLetter extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['employee_id', 'content'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}