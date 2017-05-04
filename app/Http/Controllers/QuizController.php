<?php

namespace App\Http\Controllers;

use App\Events\SendQuestion;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Question;
use App\Models\Answer;
use App\User;
use Auth;
use Illuminate\Support\Facades\Redis;
use Event;

class QuizController extends Controller
{


    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);

    }

    public function getQuestion($room_id)
    {

        $questions_numbs = Redis::lrange('room:' . $room_id, 0, 100);
        $question = Question::with('answers')->whereNotIn('id', $questions_numbs)->inRandomOrder()->first();
        Redis::rpush('room:' . $room_id, $question->id);

        Event::fire(new SendQuestion($room_id,$question));

        return ;
    }
}
