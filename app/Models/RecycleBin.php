<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class RecycleBin extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'module',
        'record_id',
        'data',
        'deleted_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
