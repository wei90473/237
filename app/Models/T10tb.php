<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T10tb extends Model
{
    protected $table = 't10tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'course' ,'idno' ,'handoutno');
}