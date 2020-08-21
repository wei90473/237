<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_bed extends Model
{
    protected $table = 'edu_bed';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('bedno', 'bedroom', 'floorno', 'isuse','roomname');
}