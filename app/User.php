<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password', 'email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'id'
    ];

    public function roles() {
        return $this->belongsToMany('App\Models\Role');
    }

    public function rooms() {
        return $this->belongsToMany('App\Models\Room');
    }

    public function quizzes() {
        return $this->hasMany('App\Models\PrivateQuiz');
    }
}
