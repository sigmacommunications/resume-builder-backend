<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateResponse extends Model
{
    use HasFactory;
	protected $guarded =[];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function templateAssign()
    {
        return $this->belongsTo(TemplateAssign::class, 'template_assign_id');
    }
}
