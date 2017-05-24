<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalResult extends Model
{
    protected $fillable = [
        'user_id',
        'points'
    ];

    public $timestamps = false;

    public static function saveResults(IntermediateResult $result)
    {
        return self::create([
            'user_id' => $result->user->id,
            'points' => $result->points,
        ]);
    }
}
