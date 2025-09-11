<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id','reviewer_id','assessment_date',
        'comment_1','comment_2','comment_3','comment_4','comment_5',
        'comment_6','comment_7','comment_8','comment_9','comment_10',
        'comment_11','comment_12','comment_13','comment_14','comment_15',
		'final_conclusion',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}