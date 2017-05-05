<?php

namespace App\Http\Controllers\Admin;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Response;
use Validator;
//use Auth;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

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

        return Question::paginate();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create()
//    {
//        //
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'question_text' => 'required | unique:questions',
            'answers' => 'array',
            'answers.*.answer_text' => 'required'
        ]);

        if ($validator->fails()) {
            $data = $validator->errors()->all();
            return response()->json($data);
        }

        $data = $request->all();
        $new_question = new Question([
            'question_text' => $data['question_text'],
        ]);


        if ($new_question->save())
        {

            foreach ($data['answers'] as $answer) {

                $new_question->answers()->create($answer);
            }

            $response = [
                'message' => 'success',
            ];

        }
        else
        {

            $response = [
                'message' => 'failure',
            ];

        }

        return response()->json($response);

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


            return $question;

        }
        else
        {

            $data = [
                'innerCode' => '404',
                'message' => 'not found',
            ];

            return response()->json($data)->setStatusCode(404);

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id)
//    {
//        //
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $question = Question::find($id);

        if (!$question)
        {

            $data = [
                'innerCode' => '404',
                'message' => 'not found',
            ];

            return response()->json($data)->setStatusCode(404);

        }

        $validator = Validator::make($request->all(), [
            'question_text' => 'required',
            'answers' => 'array',
            'answers.*.id' => 'sometimes | numeric',
            'answers.*.answer_text' => 'required'
        ]);

        if ($validator->fails()) {
            $data = $validator->errors()->all();
            return response()->json($data);
        }

        $data = $request->all();

        $question->question_text = $data['question_text'];
        $question->save();
        if ($question->save())
        {

            $answers = [];

            foreach ($data['answers'] as $answer) {

                if(isset($answer['id'])) {
                    if($answer_to_update = Answer::find($answer['id'])) {
                        $answer_to_update->answer_text = $answer['answer_text'];
                        $answers[] = $answer_to_update;
                    }
                    else {
                        $answers[] = new Answer( $answer );
                    }
                } else {
                    $answers[] = new Answer( $answer );
                }

            }

            $question->answers()->saveMany($answers);

            $response = [
                'message' => 'success',
            ];

        }
        else
        {

            $response = [
                'message' => 'failure',
            ];

        }

        return response()->json($response);
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

            $data = [
                'innerCode' => '404',
                'message' => 'not found',
            ];

            return response()->json($data)->setStatusCode(404);

        }

        $question->delete();

        $response = [
            'message' => 'success',
        ];

        return response()->json($response);

    }



}
