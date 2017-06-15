<?php
namespace App\Services;

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Redis;

use App\Models\FinalResult;
use App\Models\IntermediateResult;
use App\Models\Room;
use App\Models\Question;

use Event;
use App\Events\SendQuestion;
use App\Events\SendIntermediateResults;
use App\Helpers\SendJsonResponse;

class QuizService
{
    public static function startRound(Room $room)
    {
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

        self::sendQuestion($room->id);

        sleep(QuizController::QUESTION_TIME);

        $is_finished = Redis::get('room:'.$room->id.':'.$step.':finished');

        if ( $is_finished == 1 ) {
            return false;
        }

        self::sendResults($room);

        return true;
    }

    public static function sendResults(Room $room)
    {
        $step = Redis::get('room:'.$room->id.':step');

        $intermidiateResults = IntermediateResult::getRoomResults($room->id,$step);

        if ($step == QuizController::STEPS_COUNT )
        {
            foreach ($intermidiateResults as $result) {
                FinalResult::saveResults($result);
            }
            $room->close();

            Event::fire(new SendIntermediateResults($room->id, $intermidiateResults));

            return SendJsonResponse::sendWithMessage('Quiz finished');
        }

        Event::fire(new SendIntermediateResults($room->id, $intermidiateResults));

        sleep(QuizController::RESULTS_TIME);

        $step++;
        Redis::set('room:'.$room->id.':'.$step.':finished', 0);
        Redis::set('room:'.$room->id.':results', 0);
        Redis::set('room:'.$room->id.':step', $step);

        self::startRound($room);

        return true;
    }

    public static function sendQuestion($roomID)
    {
        $questions_numbs = Redis::lrange('room:' . $roomID, 0, 100);
        $question = Question::with(['answers' => function ($query) {
            $query->select('id', 'answer_text', 'question_id');
        }])->whereNotIn('id', $questions_numbs)->inRandomOrder()->first();
        Redis::rpush('room:' . $roomID, $question->id);

        Event::fire(new SendQuestion($roomID, $question->question_text, $question->answers));

        return SendJsonResponse::sendWithMessage('success');
    }

}