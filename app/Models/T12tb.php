<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T12tb extends Model
{
    protected $table = 't12tb';

    public $timestamps = false;

    protected $fillable = array('name' ,'idno','year','serno','taxorgan','taxcode','incomemk','type','idkind','companyno','total','net','deduct','identcode','spmk','errormk','address','idno','filedate');

}