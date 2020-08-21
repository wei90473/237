<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T52tb extends Model
{
    protected $table = 't52tb';

    public $timestamps = false;

    // protected $fillable = array('class', 'term', 'times', 'copy', 'fillsdate', 'filledate', 'upddate');
}