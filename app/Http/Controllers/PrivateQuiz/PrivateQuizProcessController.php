<?php

namespace App\Http\Controllers\PrivateQuiz;

use App\Models\PrivateQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Redis;

use App\Models\Room;
use App\Models\PrivateQuiz;
use App\Models\IntermediateResult;
use App\Services\QuizService;
use App\Events\Event;
use App\Events\PlayerAnswered;
use App\Helpers\SendJsonResponse;

class PrivateQuizProcessController extends Controller
{
    const QUESTION_TIME = 15;
    const RESULTS_TIME = 10;
    const SCORE_COEFFICIENT = 15000;

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    public function initQuiz(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $quiz = QuizService::getPrivateQuiz($user,$data['quiz_id'], true);

        if (!$quiz instanceof PrivateQuiz) {
            return $quiz;
        }

        $room = Room::with('admin')->with('users')->find($data['room']);

        if ($user->id != $room->admin->id)
        {
            return response()->json('Unauthorized', 401);
        }

        $stepsCount = ($data['stepsCount'] <= count($quiz->questions)) ? $data['stepsCount'] : count($quiz->questions);

        $room->startPrivateQuiz($quiz->id, $stepsCount);

        QuizService::startRound($room);

        return SendJsonResponse::sendWithMessage('Quiz Complete!');
    }

    public function checkResult (Request $request)
    {
        $data = $request->all();
        $points = 0;
        $step = $data['step'];
        $room = Room::with('users')->find($data['room']);
        $user = Auth::user();

        $question = PrivateQuestion::with(['answers' => function($query){
            $query->where('is_right', true)->take(1);
        }])->find($data['question']);

        $right_answer = $question->answers[0];

        if( $data['answer'] == $right_answer->id ) {
            $points = self::SCORE_COEFFICIENT / $data['time'];
        }

        $intResult = IntermediateResult::where('user_id', $user->id)->where('room_id', $room->id)->where('step',$step)->first();
        $intResult->points = $intResult->points + $points;

        if($intResult->save())
        {
            $players = Redis::get('room:'.$room->id.':players');
            $resultsCount = Redis::get('room:'.$room->id.':results');
            $resultsCount++;

            Event::fire(new PlayerAnswered($room->id, $user->name));

            if ( $resultsCount >= $players )
            {
                Redis::set('room:'.$room->id.':'.$step.':finished', 1);
                QuizService::sendResults($room, true);
                return SendJsonResponse::sendWithMessage('success');
            }

            Redis::set('room:'.$room->id.':results', $resultsCount);
            return SendJsonResponse::sendWithMessage('success');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }
}
