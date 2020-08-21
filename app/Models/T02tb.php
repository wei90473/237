<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class T02tb extends Authenticatable
{
    protected $table = 't02tb';
    protected $casts = ['class' => 'string'];
    
    public $timestamps = false;
    public $primaryKey = "class";

    protected $fillable = array();

    function m13tb()
    {
        return $this->belongsTo('App\Models\M13tb', 'organ', 'organ');
    }

    function min_grade_m17tb()
    {
        return $this->hasOne('App\Models\M17tb', 'organ', 'organ')->selectRaw('organ, min(grade) min_grade')->groupBy(['organ']);
    }

    function m07tb()
    {
        return $this->belongsTo('App\Models\M07tb', 'organ', 'agency');
    }

}
