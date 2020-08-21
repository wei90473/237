<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T92tb extends Model
{
    protected $table = 't92tb';

    public $timestamps = false;

    protected $fillable = array('paidday' ,'serno' ,'name' ,'idno' ,'type' ,'intamt' ,'extamt' ,'deduct' ,'class' ,'term' ,'course' ,'accname');
}