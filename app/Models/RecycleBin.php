<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecycleBin extends Model
{
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
