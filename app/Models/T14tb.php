<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T14tb extends Model
{
    protected $table = 't14tb';

    public $timestamps = false;

    protected $fillable = array('class', 'term', 'idno', 'serno', 'sdate', 'edate', 'stime', 'etime', 'type', 'hour', 'reason');


    function t04tb()
    {
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);
    }

    function t13tb()
    {
        return $this->belongsTo('App\Models\T13tb', 'class', 'class')->where('term', '=', $this->term)->where('idno', '=', $this->idno);;
    }

}