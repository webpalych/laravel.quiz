<?php

namespace App\Http\Controllers;

use App\Events\CheckResult;
use Auth;
use Event;
use Illuminate\Support\Facades\Redis;

use App\Events\SendQuestion;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Models\Question;
use App\Models\Answer;
use App\Models\IntermediateResult;
use App\User;




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

        return response()->json();

    }

    public function checkResult (Request $request)
    {
        $data = $request->all();
        $points = 0;
        $user = Auth::user();


        $question = Question::with(['answers' => function($query){
            $query->where('is_right','1')->take(1);
        }])->find($data['question']);

        $right_answer = $question->answers[0];

        if( $data['answer'] == $right_answer->id ) {
            $points = 1;
            // умножение очков на время $data['time']
        }

        $intResult = new IntermediateResult([
            'points' => $points,
            'step' => $data['step']
        ]);

        $intResult->user()->associate($user);

        $intResult->room()->associate($data['room']);

       if($intResult->save()){
           Event::fire(new CheckResult($data['room'], $user, $intResult));
       }

        return response()->json('success');

    }



}
