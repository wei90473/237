<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T84tb extends Model
{
    protected $table = 't84tb';

    protected $guarded = [];

    function t04tb(){
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);
    }

    function m02tb(){
        return $this->belongsTo('App\Models\M02tb', 'class', 'class')->where('term', '=', $this->term);
    }

    function t13tb(){
        return $this->belongsTo('App\Models\T13tb', 'class', 'class')->where('term', '=', $this->term)->where('idno', '=', $this->idno);
    }
}