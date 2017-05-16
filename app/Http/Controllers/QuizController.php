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
        while ($step <= 5)
        {
            $points = 0;

            if ( $step > 1 ) {
                $prevPoints = IntermediateResult::select('points')->where('step', $step - 1 )->where('user_id', $user->id)->first();
                $points = $prevPoints->points;
            }

            foreach ($room->users as $user)
            {
                $user->intResults()->create([
                    'step' => $step,
                    'points' => $points,
                    'room_id' => $room->id
                ]);
            }

            $this->callAction('sendQuestion', ['params' => ['roomID' => $room->id,]]);

            sleep(16);

            $intermidiateResults = IntermediateResult::getRoomResults($room->id,$step);

            if ($step == 5 ) {
                foreach ($intermidiateResults as $result) {
                    FinalResult::saveResults($result);
                }
                $room->close();
            }

            Event::fire(new SendIntermediateResults($room->id, $intermidiateResults));

            sleep(10);

            $step++;
        }

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
        $user = Auth::user();

        $question = Question::with(['answers' => function($query){
            $query->where('is_right','1')->take(1);
        }])->find($data['question']);

        $right_answer = $question->answers[0];

        if( $data['answer'] == $right_answer->id ) {
            $points = 15000 / $data['time'];
        }

        $intResult = IntermediateResult::where('user_id', $user->id)->where('room_id', $data['room'])->where('step',$step)->first();
        $intResult->points = $intResult->points + $points;

        if($intResult->save()){
            return SendJsonResponse::sendWithMessage('success');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }
}
