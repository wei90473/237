<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M11tb extends Model
{
    protected $table = 'm11tb_new';

    public $timestamps = false;

    protected $fillable = array('progid' ,'progname' ,'logmk');
}