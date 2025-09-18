<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'work_date',
        'start_time',
        'end_time',
		'project_name',
        'comment'
    ];

    protected $dates = [
        'start_time',
        'end_time',
        'work_date'
    ];
	
	protected $casts = [
		'start_time' => 'datetime',
		'end_time' => 'datetime'
	];

    public function employee()
	{
		return $this->belongsTo(Employee::class, 'employee_id');
	}
}
