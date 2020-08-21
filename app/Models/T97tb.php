<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T97tb extends Model
{
    protected $table = 't97tb';

    public $timestamps = false;

    protected $fillable = array('site', 'date', 'stime', 'etime', 'time', 'class', 'term');
}