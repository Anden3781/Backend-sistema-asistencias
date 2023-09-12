<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'user_id',
        'evaluation_type'
    ];

    protected $hidden = [
      'created_at',
      'updated_at',
    ];

    public function user(){
      return $this->belongsTo(User::class);
    }

    public function evaluationType(){
      return $this->belongsTo(EvaluationTypes::class, 'id');
    }
   
    public function notes()  
    {
      return $this->hasMany(Note::class); 
    }
}