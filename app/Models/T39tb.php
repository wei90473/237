<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T39tb extends Model
{
    protected $table = 't39tb';

    // public $timestamps = false;

    public $guarded = [];

    function t04tb(){
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);
    }
}