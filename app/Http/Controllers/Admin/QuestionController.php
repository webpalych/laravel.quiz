<?php

namespace App\Http\Controllers\Admin;

use App\Models\Question;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Requests\SaveQuestionRequest;

use App\Http\Controllers\Controller;
use App\Helpers\SendJsonResponse;

class QuestionController extends Controller
{


    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);

        $this->middleware('App\Http\Middleware\AdminAccess');

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Question::paginate());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveQuestionRequest $request)
    {

        $data = $request->all();
        $new_question = new Question([
            'question_text' => $data['question_text'],
        ]);

        if ($new_question->save())
        {
            $new_question->saveWithAnswers($data['answers']);

            return SendJsonResponse::sendWithMessage('success');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::with('answers')->find($id);

        if ($question)
        {
            return response()->json($question);
        }
        return SendJsonResponse::sendNotFound();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuestionRequest $request, $id)
    {
        $question = Question::find($id);

        if (!$question)
        {
            return SendJsonResponse::sendNotFound();
        }

        $data = $request->all();

        $question->question_text = $data['question_text'];
        $question->save();

        if ($question->save())
        {
            $question->saveWithAnswers($data['answers']);
            return SendJsonResponse::sendWithMessage('success');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question = Question::find($id);

        if (!$question)
        {
            return SendJsonResponse::sendNotFound();
        }

        $question->delete();

        return SendJsonResponse::sendWithMessage('success');
    }



}
