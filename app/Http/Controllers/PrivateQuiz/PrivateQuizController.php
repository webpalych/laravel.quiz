<?php

namespace App\Http\Controllers\PrivateQuiz;

use App\Helpers\CheckQuizOwner;
use App\Models\PrivateQuiz;
use Illuminate\Http\Request;
use App\Helpers\SendJsonResponse;
use App\Http\Requests\UpdateQuizRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Mockery\Exception;

class PrivateQuizController extends Controller
{

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Auth::user()->quizzes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpdateQuizRequest $request)
    {
        $user = Auth::user();

        $data = $request->all();
        $quiz = new PrivateQuiz([
            'quiz_name' => $data['quiz_name']
        ]);
        $quiz->user()->associate($user);

        try
        {
            if($quiz->save())
            {
                return SendJsonResponse::sendWithMessage('success');
            }
        }
        catch (Exception $e)
        {
            return SendJsonResponse::sendWithMessage('failure');
        }

        return SendJsonResponse::sendWithMessage('fail');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $quiz = PrivateQuiz::find($id);

        if (!$quiz)
        {
            return SendJsonResponse::sendNotFound();
        }

        if (!CheckQuizOwner::check($user, $quiz))
        {
            return response()->json('Unauthorized', 401);
        }

        return response()->json($quiz);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuizRequest $request, $id)
    {
        $user = Auth::user();
        $quiz = PrivateQuiz::find($id);

        if (!$quiz)
        {
            return SendJsonResponse::sendNotFound();
        }

        if (!CheckQuizOwner::check($user, $quiz))
        {
            return response()->json('Unauthorized', 401);
        }

        $data = $request->all();

        $quiz->quiz_name = $data['quiz_name'];
        $quiz->user()->associate($user);

        try
        {
            if($quiz->save())
            {
                return SendJsonResponse::sendWithMessage('success');
            }
        }
        catch (Exception $e)
        {
            return SendJsonResponse::sendWithMessage('failure');
        }

        return SendJsonResponse::sendWithMessage('fail');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $quiz = PrivateQuiz::find($id);

        if (!$quiz)
        {
            return SendJsonResponse::sendNotFound();
        }

        if (!CheckQuizOwner::check($user, $quiz))
        {
            return response()->json('Unauthorized', 401);
        }

        try
        {
            if($quiz->delete())
            {
                return SendJsonResponse::sendWithMessage('success');
            }
        }
        catch (Exception $e)
        {
            return SendJsonResponse::sendWithMessage('failure');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }
}
