<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T57tb extends Model
{
    protected $table = 't57tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'times' ,'whoper' ,'teaper' ,'conper' ,'envper' ,'fooper' ,'boaper' ,'worper' ,'totper' ,'offper' ,'attper' ,'recrate');
}