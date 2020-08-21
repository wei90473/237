<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Class_weeks extends Model
{

    protected $table = 'class_weeks';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'class', 'sdate', 'edate');
}