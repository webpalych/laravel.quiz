<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Auth;
use Event;
use App\Events\RoomChanges;
use Illuminate\Support\Facades\Redis;
use App\Helpers\SendJsonResponse;



class RoomController extends Controller
{

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }


    public function getAllRoomPlayers($room_id)
    {
        $room = Room::with('users')->find($room_id);

        if (!$room)
        {
            return SendJsonResponse::sendNotFound();
        }

        $users = [];

        foreach ($room->users as $user)
        {
            $users[]['name'] = $user->name;
        }

        $data = [
            'roomID' => $room->id,
            'users' => $users
        ];

        return response()->json($data);
    }

    public function create()
    {
        $user = Auth::user();
        $room = Room::create();
        $room->admin()->associate($user);
        $room->save();

        $data = [
            'message' => 'success',
            'roomID' => $room->id
        ];

        return response()->json($data);
    }

    public function join($id)
    {
        $room = Room::find($id);

        if(!$room)
        {
            return SendJsonResponse::sendNotFound();
        }

        if($room->quizStarted()) {
            return SendJsonResponse::sendWithMessage('Sorry, Quiz already started!');
        }

        $user = Auth::user();
        $room->users()->sync([$user->id], false);

        Event::fire(new RoomChanges($room, $user));

        return SendJsonResponse::sendWithMessage('success');
    }

    public function leave($id)
    {
        $user = Auth::user();

        $room = Room::find($id);

        if(!$room) {
            return SendJsonResponse::sendNotFound();
        }

        $room->users()->detach($user->id);

        if(empty($room->users->all())) {

            return $this->callAction('close', ['params' => [
                'roomID' => $room->id,
            ]]);

        }

        Event::fire(new RoomChanges($room, $user, 'left'));

        return SendJsonResponse::sendWithMessage('success');
    }

    public function close($data)
    {
        $room = Room::find($data['roomID']);

        if (!$room)
        {
            return SendJsonResponse::sendNotFound();
        }

        if ($room->close())
        {
            return SendJsonResponse::sendWithMessage('success');
        }

        return SendJsonResponse::sendWithMessage('failure');
    }

    public function isRoomAdmin($roomID)
    {
        $room = Room::with('admin')->with('users')->find($roomID);
        $user = Auth::user();

        if ($user->id != $room->admin->id)
        {
            return SendJsonResponse::sendWithMessage('false');
        }

        return SendJsonResponse::sendWithMessage('true');
    }
}
