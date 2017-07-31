<?php

namespace App\Helpers;


use App\Models\PrivateQuiz;
use App\User;

class CheckQuizOwner
{
    static public function check(User $user, PrivateQuiz $quiz)
    {
        if ($user->id != $quiz->user->id)
        {
            return false;
        }
        return true;
    }
}