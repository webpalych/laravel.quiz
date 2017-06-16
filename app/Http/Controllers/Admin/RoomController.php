<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);

        $this->middleware('App\Http\Middleware\AdminAccess');
    }

    public function getPublicRooms ()
    {
        return response()->json(Room::where([
            ['is_public', '=', '1'],
            ['is_started', '=', '1'],
        ])->get());
    }
}