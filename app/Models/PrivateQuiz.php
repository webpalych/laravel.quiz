<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateQuiz extends Model
{
    protected $fillable = ['quiz_name', 'user_id'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo( 'App\User');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\PrivateQuestion', 'quiz_id');
    }
}
