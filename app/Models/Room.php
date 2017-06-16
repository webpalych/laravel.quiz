<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;


class Room extends Model
{
    protected $fillable = [
        'is_started',
        'room_admin',
        'is_public'
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
        if($this->is_started == '1')
        {
            return true;
        }
        return false;
    }

    public function startQuiz($lang, $stepsCount)
    {
        $step = 1;
        $countPlayers = count($this->users);
        Redis::set('room:'.$this->id.':step', $step);
        Redis::set('room:'.$this->id.':'.$step.':finished', 0);
        Redis::set('room:'.$this->id.':players', $countPlayers);
        Redis::set('room:'.$this->id.':results', 0);
        Redis::set('room:'.$this->id.':language', $lang);
        Redis::set('room:'.$this->id.':stepsCount', $stepsCount);

        $this->is_started = '1';
        return $this->save();
    }

    public function close()
    {
        $this->delete();
        Redis::del('room:' . $this->id);
        Redis::del('room:'.$this->id.':step');
        Redis::del('room:'.$this->id.':players');
        Redis::del('room:'.$this->id.':results');
        $steps = Redis::get('room:'.$this->id.':stepsCount');
        for ( $i=1 ; $i <= $steps; $i++) {
            Redis::del('room:'.$this->id.':'.$i.':finished');
        }
        Redis::del('room:'.$this->id.':stepsCount');
        Redis::del('room:'.$this->id.':language');
        return true;
    }
}
