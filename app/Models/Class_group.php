<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Class_group extends Model
{

    protected $table = 'class_group';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id','groupid', 'class_group',  'class', 'name','branchcode');
}