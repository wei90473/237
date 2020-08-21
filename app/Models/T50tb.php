<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T50tb extends Model
{
    protected $table = 't50tb';

    public $timestamps = false;

    protected $fillable = array('serno' ,'sequence' ,'item' ,'unit' ,'price','quantity' ,'copy' ,'type' ,'remark' );
}