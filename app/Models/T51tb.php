<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T51tb extends Model
{
    protected $table = 't51tb';

    public $timestamps = false;

    protected $fillable = array('id' ,'class' ,'term' ,'organ' ,'quota' ,'share' ,'status' ,'oneself' ,'pubsdate' ,'pubedate');

    function m17tb()
    {
        return $this->belongsTo('App\Models\M17tb', 'organ', 'enrollorg');
    }

    function t69tb()
    {
        return $this->hasOne('App\Models\T69tb', 'organ', 'organ')->where('class', '=', $this->class);
    }   
    
}