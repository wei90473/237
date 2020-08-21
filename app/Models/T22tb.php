<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T22tb extends Model
{
    protected $table = 't22tb';

     public $timestamps = false;

    protected $fillable = array('site' ,'date' ,'stime' ,'etime' ,'time' ,'cnt' ,'reserve' ,'liaison' ,'purpose' ,'class' ,'term' ,'seattype' ,'fee' ,'request' ,'affirm' ,'status' ,'usertype' ,'keeptype' ,'bqno' ,'bqname' ,'email' ,'upddate' ,'releasemk');
}