<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'file_name',
        'file_path',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
