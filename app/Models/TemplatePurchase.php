<?php
// app/Models/TemplatePurchase.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemplatePurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','template_id','category_id','amount','currency',
        'status','payment_method','transaction_id','meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function template() 
    { 
        return $this->belongsTo(Template::class); 
    }

    public function purchases() {
        return $this->hasMany(TemplatePurchase::class);
    }
}
