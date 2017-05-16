<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Room extends Model
{
    protected $fillable = [
        'is_started',
        'room_admin'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'room_admin'
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function admin()
    {
        return $this->belongsTo( 'App\User', 'room_admin');
    }

    public function quizStarted()
    {
        if($this->is_started == '1') {
            return true;
        }
        return false;
    }

    public function startQuiz()
    {
        $this->is_started = '1';
        return $this->save();
    }

    public function close()
    {
        $this->delete();
        Redis::del('room:' . $this->id);
        return true;
    }

}
