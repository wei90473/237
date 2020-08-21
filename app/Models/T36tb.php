<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T36tb extends Model
{
    protected $table = 't36tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'date' ,'site','site_branch');
}