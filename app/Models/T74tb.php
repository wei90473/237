<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T74tb extends Model
{
    protected $table = 't74tb';

    public $timestamps = false;

    protected $fillable = array('yerly','mon','type','termcnt','headcnt','daycnt','hourcnt');

}
