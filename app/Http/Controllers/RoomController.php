<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Room;
use App\User;
use Illuminate\Support\Facades\Input;


class RoomController extends Controller
{
    public function create() {

        $room = Room::create();

        $data = [
          'message' => 'room created - ' . $room->id,
        ];

        return response()->json($data);
    }

    public function join($id) {

        $user = User::find(Input::get('user_id'));

        $room = Room::find($id);

        if(!$room) {

            return response()->json('',404);

        }

        $user->rooms()->attach($id);

        $data = [
            'message' => 'joined room - ' . $id,
        ];

        return response()->json($data);

    }

    public function leave($id) {

        $user = User::find(Input::get('user_id'));

        $room = Room::find($id);

        if(!$room) {

            return response()->json('',404);

        }

        $user->rooms()->detach($id);

        $data = [
            'message' => 'leaved room - ' . $id,
        ];

        return response()->json($data);

    }

    public function  close($id) {

        $room = Room::find($id);

        if (!$room) {
            return response()->json('',404);
        }


        if ($room->delete()) {

            $data = [
                'message' => 'room closed - ' . $id,
            ];

            return response()->json($data);
        }

        return response()->json('',500);

    }

}
