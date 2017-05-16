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

    public function saveWithAnswers($answers)
    {
        $answersToSave = [];
        foreach ($answers as $answer) {
            if(isset($answer['id'])) {
                if($answer_to_update = Answer::find($answer['id'])) {
                    $answer_to_update->answer_text = $answer['answer_text'];
                    $answersToSave[] = $answer_to_update;
                }
                else {
                    $answersToSave[] = new Answer( $answer );
                }
            } else {
                $answersToSave[] = new Answer( $answer );
            }
        }
        $this->answers()->saveMany($answersToSave);

        return $this;
    }
}
