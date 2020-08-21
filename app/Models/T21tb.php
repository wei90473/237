<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T21tb extends Model
{
    protected $table = 't21tb';

     public $timestamps = false;

    protected $fillable = array('meet' ,'serno' ,'site' ,'date' ,'time' ,'stime' ,'etime' );
}