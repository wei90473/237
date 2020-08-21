<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User_group_auth extends Model
{

    protected $table = 'user_group_auth';

    public $timestamps = false;

    protected $primaryKey = 'user_group_id';

    protected $fillable = array('user_group_id', 'menu');
}