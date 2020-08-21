<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T11tb extends Model
{
    protected $table = 't11tb';

    public $timestamps = false;

    protected $fillable = array('accname' ,'idno','date','serno','postcode','postno','branch','amt','cardno','offno','girono','transfor','bank','bankcode','bankno','post');
}