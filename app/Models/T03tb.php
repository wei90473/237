<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class T03tb extends Authenticatable
{
    protected $table = 't03tb';
    protected $casts = ['class' => 'string'];
    
    public $timestamps = false;
    // public $primaryKey = "class";

    // protected $fillable = array();
    protected $guarded = [];

}
