<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntermediateResult extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'step',
        'points'
    ];

    protected $hidden = [
        'user_id',
        'room_id',
        'id'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    public static function getRoomResults($roomID, $step)
    {
        return self::where('room_id',$roomID)->where('step',$step)->with('user')->get();
    }
}
