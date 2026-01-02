<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Team extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'parent_employee_id', // Manager (logged-in employee)
        'employee_id',        // Team member
    ];

    /**
     * Get the manager (parent employee) of the team.
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'parent_employee_id');
    }

    /**
     * Get the team member (employee) assigned under the manager.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}