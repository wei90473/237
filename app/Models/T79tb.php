<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T79tb extends Model
{
    protected $table = 't79tb';

    public $timestamps = false;

    protected $primaryKey = 'yerly';

    protected $fillable = array('yerly',  'times',  'organ' ,  'type' ,  'subsdate',  'subedate' ,  'purpose' );
}