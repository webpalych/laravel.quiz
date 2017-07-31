<?php

namespace App\Models;

use App\Interfaces\QuestionInterface;
use Illuminate\Database\Eloquent\Model;

class Question extends Model implements QuestionInterface
{
    protected $fillable = ['question_text', 'language_id'];

    public $timestamps = false;

    public function answers()
    {
        return $this->hasMany('App\Models\Answer');
    }

    public function language()
    {
        return $this->belongsTo( 'App\Models\Language');
    }
}
