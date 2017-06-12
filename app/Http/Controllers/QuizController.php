<?php

namespace App\Http\Controllers;

use App\Events\SendIntermediateResults;
use App\Helpers\SendJsonResponse;
use Auth;
use Event;
use Illuminate\Support\Facades\Redis;

use App\Events\SendQuestion;

use Illuminate\Http\Request;

use App\Models\Question;
use App\Models\Room;
use App\Models\IntermediateResult;
use App\Models\FinalResult;



class QuizController extends Controller
{
    const QUESTION_TIME = 15;
    const RESULTS_TIME = 10;
    const SCORE_COEFFICIENT = 15000;
    const STEPS_COUNT = 5;

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    public function initQuiz($roomID)
    {
        $room = Room::with('admin')->with('users')->find($roomID);
        $user = Auth::user();

        if ($user->id != $room->admin->id)
        {
            return response()->json('Unauthorized', 401);
        }

        $room->startQuiz();

        $step = 1;
        $countPlayers = count($room->users);
        Redis::set('room:'.$roomID.':step', $step);
        Redis::set('room:'.$roomID.':'.$step.':finished', 0);
        Redis::set('room:'.$roomID.':players', $countPlayers);
        Redis::set('room:'.$roomID.':results', 0);

        $this->callAction('startRound', ['params' => ['room' => $room,]]);

//        while ($step <= self::STEPS_COUNT)
//        {
//            $points = 0;
//
//            foreach ($room->users as $user)
//            {
//                if ( $step > 1 ) {
//                    $prevPoints = IntermediateResult::select('points')->where('step', $step - 1 )->where('user_id', $user->id)->first();
//                    $points = $prevPoints->points;
//                }
//
//                $user->intResults()->create([
//                    'step' => $step,
//                    'points' => $points,
//                    'room_id' => $room->id
//                ]);
//            }
//
//            $this->callAction('sendQuestion', ['params' => ['roomID' => $room->id,]]);
//
//            sleep(self::QUESTION_TIME);
//
//            $intermidiateResults = IntermediateResult::getRoomResults($room->id,$step);
//
//            if ($step == 5 ) {
//                foreach ($intermidiateResults as $result) {
//                    FinalResult::saveResults($result);
//                }
//                $room->close();
//            }
//
//            Event::fire(new SendIntermediateResults($room->id, $intermidiateResults));
//
//            sleep(self::RESULTS_TIME);
//
//            $step++;
//        }

        return response()->json($room);
    }

    public function sendQuestion($data)
    {
        $roomID = $data['roomID'];

        $questions_numbs = Redis::lrange('room:' . $roomID, 0, 100);
        $question = Question::with('answers')->whereNotIn('id', $questions_numbs)->inRandomOrder()->first();
        Redis::rpush('room:' . $roomID, $question->id);

        Event::fire(new SendQuestion($roomID,$question));

        return SendJsonResponse::sendWithMessage('success');
    }

    public function checkResult (Request $request)
    {
        $data = $request->all();
        $points = 0;
        $step = $data['step'];
        $room = Room::with('users')->find($data['room']);
        $user = Auth::user();

        $question = Question::with(['answers' => function($query){
            $query->where('is_right','1')->take(1);
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

            if ( $resultsCount >= $players )
            {
                Redis::set('room:'.$room->id.':'.$step.':finished', 1);
                $this->callAction('sendResults', ['params' => ['room' => $room]]);
                return SendJsonResponse::sendWithMessage('success');
            }

            Redis::set('room:'.$room->id.':results', $resultsCount);
            return SendJsonResponse::sendWithMessage('success');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }

    public function startRound($data)
    {
        $room = $data['room'];
        $step = Redis::get('room:'.$room->id.':step');

        $points = 0;

        foreach ($room->users as $user)
        {
            if ( $step > 1 ) {
                $prevPoints = IntermediateResult::select('points')->where('step', $step - 1 )->where('user_id', $user->id)->first();
                $points = $prevPoints->points;
            }

            $user->intResults()->create([
                'step' => $step,
                'points' => $points,
                'room_id' => $room->id
            ]);
        }

        $this->callAction('sendQuestion', ['params' => ['roomID' => $room->id,]]);

        sleep(self::QUESTION_TIME);

        $is_finished = Redis::get('room:'.$room->id.':'.$step.':finished');


        if ( $is_finished == 1 ) {
            return;
        }

        $this->callAction('sendResults', ['params' => ['room' => $room,]]);
    }

    public function sendResults($data)
    {
        $room = $data['room'];
        $step = Redis::get('room:'.$room->id.':step');

        $intermidiateResults = IntermediateResult::getRoomResults($room->id,$step);

        if ($step == 5 )
        {
            foreach ($intermidiateResults as $result) {
                FinalResult::saveResults($result);
            }
            $room->close();

            Event::fire(new SendIntermediateResults($room->id, $intermidiateResults));

            return SendJsonResponse::sendWithMessage('Quiz finished');
        }

        Event::fire(new SendIntermediateResults($room->id, $intermidiateResults));

        sleep(self::RESULTS_TIME);

        $step++;
        Redis::set('room:'.$room->id.':'.$step.':finished', 0);
        Redis::set('room:'.$room->id.':results', 0);
        Redis::set('room:'.$room->id.':step', $step);

        $this->callAction('startRound', ['params' => ['room' => $room,]]);

        return;
    }
}
