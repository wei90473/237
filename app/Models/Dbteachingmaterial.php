<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dbteachingmaterial extends Model
{
    protected $table = 'teachingmaterial';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('m01serno', 'name', 'filename', 'COA', 'online', 'id', 'addday', 'addid');
}