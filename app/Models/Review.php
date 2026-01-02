<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Review extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'employee_id', 'project_name', 'date_from', 'date_to', 'review_given_by', 'review'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
