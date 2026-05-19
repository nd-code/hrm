<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'title',
        'holiday_date',
        'description',
        'is_optional',
        'status'
    ];
}