<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $fillable = [
		'name',
		'email',
		'employee_id',
		'phone',
		'position',
		'password',
		'pan_number',
		'address',
		'joining_date',
		'bank_details'
	];
    protected $hidden = ['password'];
}
