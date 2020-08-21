<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User_group extends Model
{

    protected $table = 'user_group';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'name');
}