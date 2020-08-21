<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class S03tb extends Model
{
    protected $table = 's03tb';

    public $timestamps = false;

    protected $fillable = array('serno' ,'name' ,'alias' ,'category' ,'indent' ,'sequence' );
}