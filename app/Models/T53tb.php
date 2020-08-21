<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T53tb extends Model
{
    protected $table = 't53tb';

    public $timestamps = false;

    protected $fillable = array('class', 'term', 'times', 'copy', 'fillsdate', 'filledate', 'upddate');
}