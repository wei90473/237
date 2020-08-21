<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T69tb extends Authenticatable
{
    protected $table = 't69tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'organ' ,'applycnt' ,'checkcnt');

    public function m17tb()
    {
        return $this->belongsTo('App\Models\M17tb', 'organ', 'enrollorg');
    }

}