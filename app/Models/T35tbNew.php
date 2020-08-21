<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T35tbNew extends Model
{
    protected $table = 't35tb_new';

    public $timestamps = false;

    // protected $fillable = array('logdate' ,'logtime' ,'userid' ,'progid' ,'type' ,'logtable' ,'content');
    protected $guarded = [];
}