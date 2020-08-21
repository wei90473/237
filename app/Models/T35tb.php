<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T35tb extends Model
{
    protected $table = 't35tb';

    public $timestamps = false;

    protected $fillable = array('logdate' ,'logtime' ,'userid' ,'progid' ,'type' ,'logtable' ,'content');
}