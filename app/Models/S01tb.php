<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class S01tb extends Authenticatable
{
    protected $table = 's01tb';

    public $timestamps = false;

    protected $fillable = array('type' ,'code' ,'name' ,'fee' ,'serno' ,'category');
}