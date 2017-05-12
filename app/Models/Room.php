<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{

    protected $hidden = [
        'created_at',
        'updated_at',
        'room_admin'
    ];

    public function users() {
        return $this->belongsToMany('App\User');
    }

    public function admin() {
        return $this->belongsTo( 'App\User', 'room_admin');
    }
}
