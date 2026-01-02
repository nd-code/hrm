<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Vendor extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name', 'contact_number', 'category', 'date', 'remark'
    ];
}