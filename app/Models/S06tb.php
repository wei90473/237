<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class S06tb extends Model
{
    protected $table = 's06tb';

    public $timestamps = false;

    protected $fillable = array('yerly' ,'acccode' ,'accname' );
}