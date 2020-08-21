<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T80tb extends Model
{
    protected $table = 't80tb';

    public $timestamps = false;

    protected $primaryKey = 'class';

    protected $fillable = array('class',  'enrollorg',  'enrollid' ,  'progress' ,  'prove' ,  'regterm' ,  'loginorg' ,  'loginuser','logindate' );
}