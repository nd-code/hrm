<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Holiday extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title',
        'holiday_date',
        'description',
        'is_optional',
        'status'
    ];
}