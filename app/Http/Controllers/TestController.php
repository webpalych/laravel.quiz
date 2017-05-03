<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

use App\Http\Requests;

class TestController extends Controller
{
    public function index() {



        $quest = Question::select()
            ->where('id','1')
            ->with('answers')
            ->first() ;
        dump($quest);



    }
}
