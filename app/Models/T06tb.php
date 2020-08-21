<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T06tb extends Model
{
    protected $table = 't06tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'course' ,'name' ,'unit' ,'hour' ,'date' ,'stime' ,'etime' ,'times' ,'okrate' ,'matter', 'category', 'is_must_read','teachingmaterial');

    public function t05tb()
    {
        return $this->hasOne('App\Models\T05tb', 'unit', 'unit')->where('class', '=', $this->class)->where('term', '=', $this->term);
    }

    public function t04tb()
    {
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', $this->term);
    }

    public function t05tbs()
    {
        return $this->hasMany('App\Models\T05tb', 'class', 'class')->where('term', '=', $this->term);
    }
}