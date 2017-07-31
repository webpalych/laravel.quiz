<?php

namespace App\Models;

use App\Interfaces\QuestionInterface;
use Illuminate\Database\Eloquent\Model;

class PrivateQuestion extends Model implements QuestionInterface
{
    protected $fillable = ['question_text', 'quiz_id'];

    public $timestamps = false;

    public function answers()
    {
        return $this->hasMany('App\Models\PrivateAnswer');
    }

    public function quiz()
    {
        return $this->belongsTo('App\Models\PrivateQuiz', 'quiz_id');
    }
}
