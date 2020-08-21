<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class S04tb extends Model
{
    protected $table = 's04tb';

    public $timestamps = false;

    protected $fillable = array('serno' ,'item' ,'unit' ,'price' ,'type' ,'remark','sequence','branch' );
}