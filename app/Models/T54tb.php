<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T54tb extends Model
{
    protected $table = 't54tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'times' ,'course' ,'idno' ,'sequence' ,'upddate');
}