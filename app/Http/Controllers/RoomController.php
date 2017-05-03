<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Room;
use App\User;
use Illuminate\Support\Facades\Input;
use Auth;



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
          'message' => 'room created - ' . $room->id,
        ];

        return response()->json($data);
    }

    public function join($id) {

        $user = Auth::user();

        $room = Room::find($id);

        if(!$room) {

            return response()->json('',404);

        }

        $room->users()->attach($user->id);

        $data = [
            'message' => 'joined room - ' . $id,
        ];

        return response()->json($data);

    }

    public function leave($id) {

        $user = Auth::user();

        $room = Room::find($id);

        if(!$room) {

            return response()->json('',404);

        }

        $room->users()->detach($user->id);

        $data = [
            'message' => 'leaved room - ' . $id,
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
            return response()->json('',404);
        }


        if ($room->delete()) {

            $data = [
                'message' => 'room closed - ' . $data['roomId'],
            ];

            return response()->json($data);
        }

        return response()->json('',500);

    }

}
