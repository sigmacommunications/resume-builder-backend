<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
	protected $guarded = [];
	
	public function employees()
	{
		return $this->belongsToMany(Employee::class, 'template_assigns', 'template_id', 'employee_id');
	}
}
