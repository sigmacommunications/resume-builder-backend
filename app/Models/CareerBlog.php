<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerBlog extends Model
{
    use HasFactory;
	protected $guarded = [];
	
	public function templateAssigns()
	{
		return $this->morphMany(TemplateAssign::class, 'assignable');
	}
}
