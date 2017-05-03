<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question_text'];


    public $timestamps = false;

    public function answers()
    {
        return $this->hasMany('App\Models\Answer');
    }


}
