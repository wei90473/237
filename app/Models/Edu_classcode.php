<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_classcode extends Model
{
    protected $table = 'edu_classcode';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('class', 'code', 'name', 'deleted','createdon', 'createdby', 'modifiedon', 'modifiedfy', 'description', 'param1'
                                , 'param2', 'param3');
}