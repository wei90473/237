<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teachingmethod extends Model
{
    protected $table = 'teaching_method';

    public $timestamps = false;

    protected $fillable = array('id','classname' ,'mode' ,'modifytime' ,'createtime');
}