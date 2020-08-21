<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T07tb extends Model
{
    protected $table = 't07tb';

    protected $guarded = [];

    function t04tb(){
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);
    }

    function getTotalamtAttribute()
    {
        $amts = ['inlectamt', 'burlectamt', 'outlectamt', 'othlectamt', 'motoramt', 'planeamt', 'noteamt', 'speakamt', 'drawamt', 'vipamt', 'doneamt', 'sinamt', 'meaamt', 'lunamt', 'dinamt', 'docamt', 'penamt', 'insamt', 'actamt', 'caramt', 'placeamt', 'teaamt', 'prizeamt', 'birthamt', 'unionamt', 'setamt', 'dishamt', 'otheramt1', 'otheramt2'];

        $total = 0;

        foreach ($amts as $amt){
            $total += $this->$amt;
        }
        return $total;
    }

}