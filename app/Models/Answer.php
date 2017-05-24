<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public $timestamps = false;

    protected $hidden = [
        'question_id',
        'is_right'
    ];

    protected $fillable = [
        'answer_text',
        'is_right',
        'question_id'
    ];
}
