<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function users() {
        return $this->belongsToMany('App\User');
    }
}
