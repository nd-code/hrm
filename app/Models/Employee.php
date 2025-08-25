<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $fillable = [
		'name',
		'email',
		'phone',
		'position',
		'password',
		'pan_number',
		'address'
	];
    protected $hidden = ['password'];
}
