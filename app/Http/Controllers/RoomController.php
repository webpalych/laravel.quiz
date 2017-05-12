<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Room;
use App\User;
use Illuminate\Support\Facades\Input;
use Auth;
use Event;
use App\Events\RoomChanges;
use Illuminate\Support\Facades\Redis;



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
            return response()->json(null,404);
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

    public function create() {

        $user = Auth::user();
        $room = Room::create();
        $room->admin()->associate($user);

        $data = [
            'message' => 'success',
            'roomID' => $room->id
        ];

        return response()->json($data);
    }

    public function join($id) {

        $user = Auth::user();

        $room = Room::find($id);

        if(!$room) {

            $data = [
                'message' => 'error',
            ];

            return response()->json($data,404);

        }

        $room->users()->sync([$user->id], false);

        $data = [
            'message' => 'success',
        ];

        Event::fire(new RoomChanges($room, $user));

        return response()->json($data);

    }

    public function leave($id) {

        $user = Auth::user();

        $room = Room::find($id);

        if(!$room) {

            $data = [
                'message' => 'error',
            ];

            return response()->json($data,404);

        }

        $room->users()->detach($user->id);

        $data = [
            'message' => 'success',
        ];

        if(empty($room->users->all())) {

            return $this->callAction('close', ['params' => [
                'roomId' => $room->id,
            ]]);

        }

        Event::fire(new RoomChanges($room, $user, 'left'));

        return response()->json($data);

    }

    public function close($data) {

        $room = Room::find($data['roomID']);

        if (!$room) {

            $data = [
                'message' => 'error',
            ];

            return response()->json($data,404);

        }


        if ($room->delete()) {

            Redis::del('room:' . $data['roomId']);

            $data = [
                'message' => 'success',
            ];

            return response()->json($data);
        }

        return response()->json('',500);

    }

}
