<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T98tb extends Model
{
    protected $table = 't98tb';

     public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'course' ,'idno' ,'method1' ,'method2' ,'method3' ,'other1' ,'other2' ,'other3' ,'mark');
}