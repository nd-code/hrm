<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class LeaveReply extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'leave_id',
        'employee_id',
        'message',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }
}