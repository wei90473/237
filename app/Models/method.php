<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Method extends Model
{
    protected $table = 'method';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'method', 'name', 'mode', 'yerly', 'modifytime');
}