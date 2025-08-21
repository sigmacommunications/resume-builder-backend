<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];
	
	public function templates()
    {
        return $this->hasMany(\App\Models\Template::class,'category_id','id');
    }
}
