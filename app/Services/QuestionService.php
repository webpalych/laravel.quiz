<?php
/**
 * Created by PhpStorm.
 * User: Palych
 * Date: 31.07.2017
 * Time: 10:46
 */

namespace App\Services;

use App\Interfaces\QuestionInterface;
use App\Models\Answer;

class QuestionService
{
    static public function safeWithAnswers (QuestionInterface $question, array $answers)
    {
        $answersToSave = [];
        foreach ($answers as $answer) {
            if(isset($answer['id'])) {
                if($answer_to_update = Answer::find($answer['id'])) {
                    $answer_to_update->answer_text = $answer['answer_text'];
                    $answer_to_update->is_right = $answer['is_right'];
                    $answersToSave[] = $answer_to_update;
                }
                else {
                    $answersToSave[] = new Answer( $answer );
                }
            } else {
                $answersToSave[] = new Answer( $answer );
            }
        }
        try {
            $question->answers()->saveMany($answersToSave);
        }
        catch (\Exception $e) {
            return false;
        }

        return true;
    }
}