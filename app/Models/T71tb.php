<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T71tb extends Authenticatable
{
    protected $table = 't71tb';

    public $timestamps = false;

    protected $fillable = array('yerly' ,'times' ,'organ' ,'content','modorgan');
}