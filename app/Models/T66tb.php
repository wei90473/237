<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T66tb extends Authenticatable
{
    protected $table = 't66tb';

    public $timestamps = false;

    protected $fillable = array('class','name','term','sdate','edate','trainday','trainhour','regcnt','passcnt','endcnt');
}