<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::get('/', function() {
   return view('index');
});

Route::group(['prefix'=>'admin'], function(){
    Route::resource('questions', 'Admin\QuestionController', ['except' => [
        'create', 'edit'
    ]]);
    Route::post('auth', 'Admin\AuthenticateController@authenticate');
});


Route::group(['prefix'=>'room'], function(){
    Route::get('create', 'RoomController@create');
    Route::get('join/{id}', 'RoomController@join');
    Route::get('leave/{id}', 'RoomController@leave');
});


Route::get('/quiz/get_question/{roomID}', 'QuizController@getQuestion');


Route::post('auth', 'Admin\AuthenticateController@registrationUser');


