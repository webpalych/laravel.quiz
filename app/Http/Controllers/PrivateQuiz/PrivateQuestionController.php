<?php

namespace App\Http\Controllers\PrivateQuiz;

use App\Http\Requests\SavePrivateQuestionRequest;
use App\Http\Requests\UpdatePrivateQuestionRequest;
use App\Models\PrivateQuestion;
use App\Models\PrivateQuiz;
use App\Helpers\SendJsonResponse;
use App\Services\QuizService;
use App\Services\QuestionService;

use App\Http\Controllers\Controller;
use Auth;

class PrivateQuestionController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }



    public function store(SavePrivateQuestionRequest $request, $quiz_id)
    {
        $user = Auth::user();
        $quiz = QuizService::getPrivateQuiz($user,$quiz_id);

        if (!$quiz instanceof PrivateQuiz) {
            return $quiz;
        }

        $data = $request->all();
        $new_question = new PrivateQuestion([
            'question_text' => $data['question_text'],
        ]);

        if ($new_question->save())
        {
            QuestionService::safeWithAnswers($new_question, $data['answers']);

            return SendJsonResponse::sendWithMessage('success');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }


    public function show($quiz_id, $question_id)
    {
        $user = Auth::user();
        $quiz = QuizService::getPrivateQuiz($user,$quiz_id);

        if (!$quiz instanceof PrivateQuiz) {
            return $quiz;
        }

        $question = PrivateQuestion::with('answers')->find($question_id);

        if ($question) {
            return response()->json($question);
        }
        return SendJsonResponse::sendNotFound();
    }


    public function update(UpdatePrivateQuestionRequest $request, $quiz_id, $question_id)
    {
        $user = Auth::user();
        $quiz = QuizService::getPrivateQuiz($user,$quiz_id);

        if (!$quiz instanceof PrivateQuiz) {
            return $quiz;
        }

        $question = PrivateQuestion::find($question_id);

        if (!$question)
        {
            return SendJsonResponse::sendNotFound();
        }
        $data = $request->all();

        $question->question_text = $data['question_text'];

        if ($question->save())
        {
            QuestionService::safeWithAnswers($question, $data['answers']);
            return SendJsonResponse::sendWithMessage('success');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }


    public function destroy($quiz_id, $question_id)
    {
        $user = Auth::user();
        $quiz = QuizService::getPrivateQuiz($user,$quiz_id);

        if (!$quiz instanceof PrivateQuiz) {
            return $quiz;
        }

        $question = PrivateQuestion::find($question_id);

        if (!$question)
        {
            return SendJsonResponse::sendNotFound();
        }

        $question->delete();

        return SendJsonResponse::sendWithMessage('success');
    }
}
