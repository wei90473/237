<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T56tb extends Model
{
    protected $table = 't56tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'times' ,'course' ,'idno' ,'serno' ,'ans1' ,'ans2' ,'ans3' ,'upddate' ,'fillmk' ,'crtdate');
}