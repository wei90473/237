<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T58tb extends Authenticatable
{
    protected $table = 't58tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'course' ,'sequence');
}