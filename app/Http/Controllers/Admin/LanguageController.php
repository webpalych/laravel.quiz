<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Language;
use App\Http\Requests\SaveLanguageRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\SendJsonResponse;

class LanguageController extends Controller
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
        return response()->json(Language::all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveLanguageRequest $request)
    {
        $data = $request->all();
        $new_language = new Language([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        if ($new_language->save())
        {
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
        $language = Language::find($id);

        if ($language)
        {
            return response()->json($language);
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
    public function update(SaveLanguageRequest $request, $id)
    {
        $language = Language::find($id);

        if (!$language)
        {
            return SendJsonResponse::sendNotFound();
        }

        $data = $request->all();

        $language->name = $data['name'];
        $language->slug = $data['slug'];
        $language->save();

        if ($language->save())
        {
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
        $language = Language::find($id);

        if (!$language)
        {
            return SendJsonResponse::sendNotFound();
        }

        $language->delete();

        return SendJsonResponse::sendWithMessage('success');
    }
}
