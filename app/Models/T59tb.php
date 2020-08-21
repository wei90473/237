<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T59tb extends Authenticatable
{
    protected $table = 't59tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'serno' ,'comment' ,'addcourse' ,'delcourse' ,'wholeval' ,'willing' ,'othercom');
}