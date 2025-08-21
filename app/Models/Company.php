<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
	protected $table = 'company';
	protected $guarded =[];
	
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function departments()
	{
		return $this->hasMany(Department::class);
	}
	
	public function employee()
	{
		return $this->hasMany(Employee::class,'company_id','id');
	}
	
	public function mails()
	{
		return $this->hasMany(Mail::class, 'user_id', 'user_id');
	}
	
	
	public function assign_templates()
	{
		return $this->hasMany(TemplateAssign::class, 'company_id', 'id');
	}
}
