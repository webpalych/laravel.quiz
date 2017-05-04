<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Room;
use App\User;
use Illuminate\Support\Facades\Input;
use Auth;
use Event;
use App\Events\JoinRoom;
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


    public function create() {

        $room = Room::create();

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

        $room->users()->attach($user->id);

        $data = [
            'message' => 'success',
        ];

        Event::fire(new JoinRoom($room,$user));

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

        return response()->json($data);

    }

    public function  close($data) {

        $room = Room::find($data['roomId']);

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
