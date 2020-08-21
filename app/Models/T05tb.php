<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T05tb extends Model
{
    protected $table = 't05tb';

    public $timestamps = false;

    protected $fillable = array('class', 'term', 'unit', 'name', 'remark');

    public function t04tb()
    {
        return $this->belongsTo('App\Models\T04tb', 'class', 'class');
    }    
}