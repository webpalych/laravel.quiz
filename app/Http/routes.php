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

Route::get('/', function()
{
   return view('index');
});

Route::group(['prefix'=>'admin'], function()
{
    Route::resource('questions', 'Admin\QuestionController', ['except' => [
        'create', 'edit'
    ]]);
    Route::resource('languages', 'Admin\LanguageController', ['except' => [
        'create', 'edit'
    ]]);
    Route::get('public_rooms', 'Admin\RoomController@getPublicRooms');
    Route::post('auth', 'Admin\AuthenticateController@authenticate');
});

Route::group(['prefix'=>'room'], function()
{
    Route::post('create', 'RoomController@create');
    Route::get('join/{id}', 'RoomController@join');
    Route::get('leave/{id}', 'RoomController@leave');
    Route::get('isAdmin/{id}', 'RoomController@isRoomAdmin');
});

Route::group(['prefix'=>'quiz'], function()
{
    Route::get('get_players/{roomID}', 'RoomController@getAllRoomPlayers');
    Route::post('check_results', 'QuizController@checkResult');
    Route::post('start_quiz', 'QuizController@initQuiz');
});

Route::post('auth', 'Admin\AuthenticateController@registrationUser');


