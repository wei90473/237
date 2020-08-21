<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employ_sort extends Model
{

    protected $table = 'employ_sort';

    public $timestamps = false;

    protected $fillable = array('class', 'term', 'idno', 'teacher_sort');
}