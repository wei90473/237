<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T37tb extends Model
{
    protected $table = 't37tb';

    public $timestamps = false;

    protected $fillable = array('type' ,'site' ,'date' ,'stime', 'etime', 'time', 'cnt', 'reserve', 'liaison', 'purpose', 'class', 'term', 'seattype', 'fee', 'request');
}