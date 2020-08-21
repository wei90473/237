<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T68tb extends Authenticatable
{
    protected $table = 't68tb';

    public $timestamps = false;

    protected $fillable = array('yerly' ,'times' ,'purpose' ,'sdate' ,'edate' ,'party','branch');
}