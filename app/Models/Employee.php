<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
	protected $guarded =[];
	
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function departments()
	{
		return $this->belongsToMany(Department::class, 'department_employee', 'employee_id', 'department_id');
	}

	
	public function templateAssigns()
	{
		return $this->hasMany(TemplateAssign::class);
	}
	
	public function company()
	{
		return $this->hasOne(Company::class,'id','company_id');
	}

	public function department()
	{
		return $this->belongsTo(Department::class);
	}
	
	public function templatesss()
	{
		return $this->belongsTo(TemplateAssign::class);
	}
	
	public function templates()
	{
		return $this->belongsToMany(Template::class, 'template_assigns', 'employee_id', 'template_id');
	}
}
