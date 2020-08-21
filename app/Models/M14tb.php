<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M14tb extends Model
{
    protected $table = 'm14tb';
    protected $casts = ['site' => 'string'];
    public $timestamps = false;
    public $primaryKey = "site";

    protected $fillable = array('site' ,'name' ,'type' ,'feetype' ,'timetype' ,'limit' ,'feea' ,'feeb' ,'feec' ,'seat' ,'door','branch','open');
}