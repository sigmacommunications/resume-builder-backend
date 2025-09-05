<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateAssign extends Model
{
    use HasFactory;
	protected $guarded =[];
	
	public function templates()
	{
		return $this->belongsTo(Template::class);
	}
	
	public function responses()
	{
		return $this->hasMany(TemplateResponse::class, 'template_assign_id');
	}

	public function assignable()
	{
		return $this->morphTo(__FUNCTION__, 'type', 'assignable_id');
	}

    public function emploaeyee()
    {
        return $this->belongsTo(Employee::class);
    }
	
	public function employee()
	{
		return $this->belongsTo(Employee::class, 'employee_id');
	}
}
