<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class web_simulation extends Model
{
    protected $table = 'web_simulation';

    public $timestamps = false;

    protected $fillable = array('md5_idno','idno','type');

}