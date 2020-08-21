<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T34tb extends Model
{
    protected $table = 't34tb';

    public $timestamps = false;

    protected $fillable = array('articleid' ,'subjectid' ,'majoridea' ,'author' ,'email' ,'institute' ,'date' ,'content');
}