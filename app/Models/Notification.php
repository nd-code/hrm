<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Notification extends Model {
    use LogsActivity;
    
    protected $fillable = ['message'];
}